<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\InventoryMovement;
use App\Models\ProductVariant;
use App\Models\InventoryStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// ┌─────────────────────────────────────────────────────────────────────────────┐
// │ CATATAN ARSITEKTUR STOK — MovementForm                                     │
// │                                                                             │
// │ Form ini menangani PENYESUAIAN STOK MANUAL oleh staff/admin                 │
// │ (mis: stok opname, barang rusak, penyesuaian fisik).                        │
// │                                                                             │
// │ Ini BERBEDA dari alur penjualan otomatis. Trigger DB                        │
// │ trg_decrement_stock_on_sale_item hanya berlaku untuk INSERT sale_items.      │
// │ Form ini langsung UPDATE inventory_stock + INSERT inventory_movements         │
// │ secara manual karena tidak ada event INSERT ke sale_items di sini.           │
// │                                                                             │
// │ Jangan gabungkan dua alur ini. Setiap alur mana yang tepat tergantung        │
// │ sumbernya: penjualan pakai trigger, koreksi manual pakai form ini.           │
// └─────────────────────────────────────────────────────────────────────────────┘

class MovementForm extends Component
{
    public string $variant_id = '';
    public string $movement_type = 'adjustment_in';
    public float  $quantity = 1;
    public string $note = '';

    protected $rules = [
        'variant_id'    => 'required|exists:product_variants,id',
        'movement_type' => 'required|in:adjustment_in,adjustment_out,write_off',
        'quantity'      => 'required|numeric|min:0.01',
        'note'          => 'nullable|string|max:500',
    ];

    protected $messages = [
        'movement_type.in' => 'Tipe pergerakan tidak valid.',
    ];

    public function save()
    {
        $this->validate();

        $staff = Auth::user();
        $store = ($staff && method_exists($staff, 'getActiveStore')) ? $staff->getActiveStore() : null;

        if (!$store) {
            $this->dispatch('toast', message: 'Toko aktif tidak ditemukan.', type: 'error');
            return;
        }

        // Tentukan arah pergerakan stok
        $quantityChange = match($this->movement_type) {
            'adjustment_in'  =>  abs($this->quantity),
            'adjustment_out',
            'write_off'      => -abs($this->quantity),
            default          =>  abs($this->quantity),
        };

        try {
            DB::transaction(function () use ($staff, $store, $quantityChange) {
                // Kunci baris stok untuk mencegah race condition
                $stock = InventoryStock::where('variant_id', $this->variant_id)
                    ->where('store_id', $store->id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    // Buat entri stok baru jika belum ada (stok 0)
                    if ($quantityChange < 0) {
                        throw new \Exception('Stok produk ini belum ada di cabang ini.');
                    }
                    $stock = InventoryStock::create([
                        'id'         => Str::uuid()->toString(),
                        'variant_id' => $this->variant_id,
                        'store_id'   => $store->id,
                        'quantity'   => 0,
                    ]);
                }

                $newQty = $stock->quantity + $quantityChange;
                if ($newQty < 0) {
                    throw new \Exception(
                        'Stok tidak mencukupi. Tersedia: ' . $stock->quantity .
                        ', dikurangi: ' . abs($quantityChange)
                    );
                }

                // Update saldo stok
                $stock->update(['quantity' => $newQty]);

                // Catat movement (manual — bukan dari trigger penjualan)
                InventoryMovement::create([
                    'id'              => Str::uuid()->toString(),
                    'variant_id'      => $this->variant_id,
                    'store_id'        => $store->id,
                    'movement_type'   => $this->movement_type,
                    'quantity_change'  => $quantityChange,
                    'reference_table' => 'manual',
                    'reference_id'    => null,
                    'staff_id'        => $staff->id,
                    'note'            => $this->note ?: 'Penyesuaian stok manual oleh ' . $staff->full_name,
                ]);
            });

            $this->dispatch('toast', message: 'Penyesuaian stok berhasil dicatat.', type: 'success');
            return $this->redirect(route('inventory.movements'), navigate: true);

        } catch (\Exception $e) {
            Log::error('MovementForm: Gagal catat stok: ' . $e->getMessage());
            $this->addError('quantity', $e->getMessage());
        }
    }

    public function render()
    {
        // Hanya tampilkan produk yang aktif dan track_stock = true
        $variants = ProductVariant::with('product')
            ->where('active', true)
            ->whereHas('product', fn ($q) => $q->where('track_stock', true)->where('active', true))
            ->orderBy('sku')
            ->get();

        return view('livewire.inventory.movement-form', [
            'variants' => $variants
        ]);
    }
}


