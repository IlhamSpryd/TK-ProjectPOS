<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\InventoryMovement;
use App\Models\ProductVariant;
use App\Models\InventoryStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovementForm extends Component
{
    public $variant_id = '';
    public $movement_type = 'IN';
    public $quantity = 1;
    public $note = '';

    protected $rules = [
        'variant_id' => 'required|exists:product_variants,id',
        'movement_type' => 'required|in:IN,OUT,ADJUSTMENT',
        'quantity' => 'required|numeric|min:0.01',
        'note' => 'nullable|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        $staff = Auth::user();
        $store = ($staff && method_exists($staff, 'getActiveStore')) ? $staff->getActiveStore() : null;

        if (!$store) {
            $this->dispatch('toast', message: 'Toko aktif tidak ditemukan. Tidak dapat memproses inventaris.', type: 'error');
            return;
        }

        DB::beginTransaction();
        try {
            $quantityChange = $this->quantity;
            if ($this->movement_type === 'OUT') {
                $quantityChange = -$this->quantity;
            } else if ($this->movement_type === 'ADJUSTMENT') {
                // Determine logic for adjustment: typically user inputs the real diff or exact amount.
                // Assuming $this->quantity here is the difference (e.g., +5 or -2). But since min is 0.01,
                // we'll just treat it as absolute amount to adjust (can be handled differently in advanced systems)
                // For now let's just use it as positive adjustment. 
            }

            // Create movement
            $movement = InventoryMovement::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'variant_id' => $this->variant_id,
                'store_id' => $store->id,
                'movement_type' => $this->movement_type,
                'quantity_change' => $quantityChange,
                'note' => $this->note,
                'staff_id' => $staff->id,
            ]);

            // Update stock
            $stock = InventoryStock::firstOrCreate(
                ['variant_id' => $this->variant_id, 'store_id' => $store->id],
                ['id' => (string) \Illuminate\Support\Str::uuid(), 'quantity' => 0]
            );

            if ($this->movement_type === 'ADJUSTMENT') {
                // If it's an exact set adjustment, $quantityChange would be the new stock.
                // But for now we treat ADJUSTMENT as adding quantity. Let's just do standard add.
                $stock->quantity += $quantityChange;
            } else {
                $stock->quantity += $quantityChange;
            }

            if ($stock->quantity < 0) {
                throw new \Exception("Stok tidak boleh kurang dari 0.");
            }

            $stock->save();

            DB::commit();

            $this->dispatch('toast', message: 'Pergerakan stok berhasil dicatat.', type: 'success');
            return $this->redirect(route('inventory.movements'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mencatat stok: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Gagal mencatat stok: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $variants = ProductVariant::with('product')->get();
        return view('livewire.inventory.movement-form', [
            'variants' => $variants
        ])->layout('layouts.app');
    }
}
