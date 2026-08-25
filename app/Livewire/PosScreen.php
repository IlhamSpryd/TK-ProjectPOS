<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Services\SaleService;
use App\Services\ProductCatalogService;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PosScreen extends Component
{
    // State Livewire hanya digunakan untuk modal dan penanganan checkout sisi server
    public bool $showSuccessModal = false;
    public ?Sale $lastSale = null;

    // Data JSON (hanya kategori dan pajak, katalog ditarik via API agar HTML tidak bengkak)
    public string $taxRatesJson = '{}';
    public string $categoriesJson = '[]';
    
    public ?string $storeId = null;
    public bool $isFnbStore = false;
    public $heldOrders = [];

    public function mount(ProductCatalogService $catalogService)
    {
        $staff = Auth::user();
        if (!$staff) return;

        $store = $staff->getActiveStore();
        if (!$store) return;

        $this->storeId = $store->id;
        $this->isFnbStore = strtolower($store->business_type) === 'f&b';

        // Persiapkan data untuk Alpine.js saat inisialisasi
        $this->taxRatesJson = json_encode($catalogService->getTaxRates($staff->tenant_id));
        $this->categoriesJson = json_encode($catalogService->getCategories($staff->tenant_id));

        // Muat pesanan yang ditahan (hold) khusus untuk toko F&B
        if ($this->isFnbStore) {
            $this->heldOrders = Sale::where('store_id', $this->storeId)
                ->where('status', 'open')
                ->with(['customer', 'items'])
                ->latest('sale_date')
                ->get();
        }
    }

    /**
     * Memproses pembayaran dari keranjang Alpine.js.
     * Menerima payload lengkap via panggilan $wire.call.
     */
    public function processPayment(array $cartPayload, string $paymentMethod, float $cashReceived, ?string $customerId, ?string $tableNumber, ?string $activeSaleId, SaleService $saleService)
    {
        if (empty($cartPayload)) {
            $this->addError('process', 'Keranjang masih kosong');
            return;
        }

        try {
            $staff = Auth::user();

            if ($activeSaleId) {
                // Selesaikan pesanan yang sebelumnya ditahan (open)
                $sale = Sale::findOrFail($activeSaleId);
                $paymentData = [
                    'payment_method' => $paymentMethod,
                    'cash_received' => $paymentMethod === 'cash' ? $cashReceived : 0,
                ];
                $sale = $saleService->finalizePayment($sale, $paymentData, $staff);
            } else {
                // Buat transaksi baru
                $data = [
                    'cart' => $cartPayload,
                    'customer_id' => $customerId,
                    'payment_method' => $paymentMethod,
                    'cash_received' => $cashReceived,
                    'table_number' => $tableNumber,
                    'notes' => '',
                ];
                $sale = $saleService->createSale($data, $staff);
            }

            $this->lastSale = $sale;
            $this->showSuccessModal = true;
            
            // Beri instruksi pada Alpine untuk mengosongkan keranjang lokal
            $this->dispatch('cart:clear');
            
            // Segarkan daftar pesanan yang ditahan jika F&B
            if ($this->isFnbStore) {
                $this->refreshHeldOrders();
            }

        } catch (BusinessException $e) {
            $this->addError('process', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Transaksi gagal', [
                'error' => $e->getMessage(),
                'staff_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('process', 'Transaksi gagal. Silakan coba lagi atau hubungi supervisor.');
        }
    }

    /**
     * Menyimpan pesanan yang ditahan (hold order) dari keranjang Alpine.js.
     */
    public function holdOrder(array $cartPayload, ?string $customerId, ?string $tableNumber, SaleService $saleService)
    {
        if (empty($cartPayload)) {
            $this->addError('process', 'Keranjang masih kosong');
            return;
        }
        try {
            $staff = Auth::user();
            $data = [
                'cart' => $cartPayload,
                'customer_id' => $customerId,
                'table_number' => $tableNumber,
                'notes' => '',
            ];
            $saleService->openOrder($data, $staff);
            
            // Kosongkan keranjang lokal via event Alpine
            $this->dispatch('cart:clear');
            
            $this->refreshHeldOrders();
        } catch (BusinessException $e) {
            $this->addError('process', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Gagal hold order', [
                'error' => $e->getMessage(),
                'staff_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('process', 'Gagal menahan pesanan. Silakan coba lagi atau hubungi supervisor.');
        }
    }

    /**
     * Memuat pesanan yang ditahan kembali ke keranjang Alpine.js.
     */
    public function loadHeldOrder(string $saleId)
    {
        $sale = Sale::with(['items.variant.product'])->findOrFail($saleId);
        
        $orderData = [
            'saleId' => $sale->id,
            'tableNumber' => $sale->table_number,
            'customerId' => $sale->customer_id,
            'items' => []
        ];

        // Format ulang item untuk store keranjang Alpine.js
        foreach ($sale->items as $item) {
            $orderData['items'][$item->variant_id] = [
                'name' => $item->product_name,
                'productName' => $item->product_name,
                'sku' => $item->variant_sku ?? '',
                'unit' => $item->unit ?? '',
                'attributes' => $item->variant_attributes ?? [],
                'price' => (float) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'discount' => (float) $item->discount,
                'imageUrl' => $item->variant?->product?->image_url,
                'stock' => 9999, // Abaikan validasi stok lokal saat memuat pesanan (bergantung pada validasi server)
                'taxCategoryId' => $item->tax_category_id
            ];
        }

        $this->dispatch('cart:load-order', $orderData);
    }
    
    public function startNewTransaction()
    {
        $this->lastSale = null;
        $this->showSuccessModal = false;
        $this->dispatch('cart:clear');
    }

    private function refreshHeldOrders()
    {
        if ($this->isFnbStore && $this->storeId) {
            $this->heldOrders = Sale::where('store_id', $this->storeId)
                ->where('status', 'open')
                ->with(['customer', 'items'])
                ->latest('sale_date')
                ->get();
        }
    }

    public function render()
    {
        // View render ringan karena data dikelola oleh client (Alpine.js)
        return view('livewire.pos-screen', [
            'customers' => \App\Models\Customer::where('active', true)->orderBy('name')->limit(100)->get()
        ])->layout('components.layouts.app', [
            'title' => 'Kasir / POS',
        ]);
    }
}
