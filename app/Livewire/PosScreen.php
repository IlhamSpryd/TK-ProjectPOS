<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Sale;
use App\Services\SaleService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class PosScreen extends Component
{
    use WithPagination;

    public string $search = '';
    public array $cart = [];
    public ?string $customer_id = null;
    public string $payment_method = 'cash';
    public $cash_received = 0;
    public $change_amount = 0;
    public ?Sale $lastSale = null;
    public $customers = [];
    
    // UI state
    public bool $showSuccessModal = false;

    public function mount()
    {
        $this->customers = Customer::where('active', true)->limit(100)->get();
    }

    public function updatedSearch()
    {
        // Akan nge-trigger re-render, perhitungan search di render()
    }

    public function addToCart($variantId)
    {
        $variant = ProductVariant::with(['product', 'product.taxCategory'])->find($variantId);
        
        if (!$variant) return;

        // Cek stok (sederhana, stok riil mungkin butuh relasi yg lebih detail dgn store)
        // Disini asumsikan jika ditambahkan ke cart, ada validasi. 
        // Logic fix nya di SaleService, tapi kita cegah di UI juga minimal punya stok.
        
        if (isset($this->cart[$variantId])) {
            $this->cart[$variantId]['quantity']++;
        } else {
            // Get stock for active store
            $staff = Auth::user();
            $store = $this->getActiveStore($staff);
            
            $stock = 0;
            if ($store) {
                $invStock = \App\Models\InventoryStock::where('store_id', $store->id)
                    ->where('variant_id', $variantId)
                    ->first();
                $stock = $invStock ? $invStock->quantity : 0;
            }

            $this->cart[$variantId] = [
                'name' => $variant->product->name . ' ' . $variant->sku,
                'sku' => $variant->sku,
                'price' => $variant->selling_price,
                'quantity' => 1,
                'stock' => $stock,
                'discount' => 0
            ];
        }

        $this->calculateTotals();
    }

    public function updateQuantity($variantId, $quantity)
    {
        if (isset($this->cart[$variantId])) {
            // Validasi jika quantity lbih besar drpd stok
            if ($quantity > $this->cart[$variantId]['stock']) {
                $this->addError('cart', 'Stok tidak mencukupi (Tersedia: ' . $this->cart[$variantId]['stock'] . ')');
                $quantity = $this->cart[$variantId]['stock'];
            }
            if ($quantity <= 0) {
                $this->removeFromCart($variantId);
            } else {
                $this->cart[$variantId]['quantity'] = $quantity;
            }
        }
        $this->calculateTotals();
    }
    
    // Tambah fungsi menambah dan mengurang qty via tombol
    public function incrementQuantity($variantId)
    {
        if (isset($this->cart[$variantId])) {
            $this->updateQuantity($variantId, $this->cart[$variantId]['quantity'] + 1);
        }
    }
    
    public function decrementQuantity($variantId)
    {
        if (isset($this->cart[$variantId])) {
            $this->updateQuantity($variantId, $this->cart[$variantId]['quantity'] - 1);
        }
    }

    public function updateDiscount($variantId, $discount)
    {
        if (isset($this->cart[$variantId])) {
            // Limit discount to not exceed the price * quantity
            $maxDiscount = $this->cart[$variantId]['price'] * $this->cart[$variantId]['quantity'];
            $discountAmount = min((float)$discount, $maxDiscount);
            $this->cart[$variantId]['discount'] = max(0, $discountAmount);
        }
        $this->calculateTotals();
    }

    public function removeFromCart($variantId)
    {
        unset($this->cart[$variantId]);
        $this->calculateTotals();
    }

    public function selectCustomer($customerId)
    {
        $this->customer_id = $customerId;
    }

    public function updatedCashReceived()
    {
        $this->calculateTotals();
    }

    public function updatedPaymentMethod()
    {
        if ($this->payment_method !== 'cash') {
            $this->cash_received = $this->getGrandTotalProperty();
        }
        $this->calculateTotals();
    }

    public function processPayment(SaleService $saleService)
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Keranjang masih kosong');
            return;
        }

        try {
            $staff = Auth::user();
            
            $data = [
                'cart' => $this->cart,
                'customer_id' => $this->customer_id,
                'payment_method' => $this->payment_method,
                'cash_received' => $this->payment_method === 'cash' ? (float) $this->cash_received : $this->getGrandTotalProperty(),
                'notes' => ''
            ];

            $sale = $saleService->createSale($data, $staff);
            
            $this->lastSale = $sale;
            $this->clearCart();
            $this->showSuccessModal = true;
            
        } catch (Exception $e) {
            $this->addError('process', $e->getMessage());
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->customer_id = null;
        $this->payment_method = 'cash';
        $this->cash_received = 0;
        $this->change_amount = 0;
    }

    public function startNewTransaction()
    {
        $this->lastSale = null;
        $this->showSuccessModal = false;
        $this->clearCart();
    }

    // Computed Properties
    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getDiscountTotalProperty()
    {
        return collect($this->cart)->sum('discount');
    }

    public function getTaxTotalProperty()
    {
        // Simplifikasi: hitung berdasarkan store default tax jika ada, tapi karena di Livewire kita harus tahu ratenya
        // Kita hitung secara kasar di UI, tapi yang akurat di backend (SaleService).
        // Untuk tampilan, mari kita ambil PPN 11% secara default jika belum tahu. 
        // Tapi mari pastikan ini tidak perlu tampil akurat tax per item sebelum proses jika susah. 
        // Kita fetch secara realtime logic di cart atau biarkan 0 dulu dan hitung tax di backend.
        // Di sini kita asumsi tax 11% untuk contoh, idealnya fetch dari product.
        // Mari kita buat 0 dulu, akan lebih presisi jika tax dihitung per baris seperti di logic service.
        $tax = 0;
        
        $staff = Auth::user();
        if(!$staff) return 0;
        
        $store = $this->getActiveStore($staff);
        if(!$store) return 0;
        
        // Batch load tax categories to fix N+1
        $taxCategoryIds = collect($this->cart)
            ->map(function ($item, $variantId) use ($store) {
                $variant = ProductVariant::with(['product'])->find($variantId);
                return $variant->product->tax_category_id ?? $store->default_tax_category_id;
            })
            ->filter()
            ->unique();
            
        $taxCategories = \App\Models\TaxCategory::whereIn('id', $taxCategoryIds)
            ->get()
            ->keyBy('id');
            
        foreach($this->cart as $variantId => $item) {
             $variant = ProductVariant::with(['product'])->find($variantId);
             $taxCategoryId = $variant->product->tax_category_id ?? $store->default_tax_category_id;
             if($taxCategoryId && isset($taxCategories[$taxCategoryId])) {
                 $taxCategory = $taxCategories[$taxCategoryId];
                 // Asumsi sederhana tanpa validasi kombinasi di sini
                 $amountBeforeTax = ($item['price'] * $item['quantity']) - $item['discount'];
                 $tax += $amountBeforeTax * ($taxCategory->rate / 100);
             }
        }
        return $tax;
    }

    public function getGrandTotalProperty()
    {
        return $this->getSubtotalProperty() - $this->getDiscountTotalProperty() + $this->getTaxTotalProperty();
    }
    
    private function calculateTotals()
    {
        $grandTotal = $this->getGrandTotalProperty();
        $this->change_amount = max(0, $this->cash_received - $grandTotal);
    }
    
    private function getActiveStore($staff)
    {
        if(!$staff) return null;
        $primaryStore = $staff->stores()->wherePivot('is_primary', true)->first();
        if ($primaryStore) {
            return $primaryStore;
        }
        return $staff->stores()->first();
    }

    public function render()
    {
        $staff = Auth::user();
        $store = $this->getActiveStore($staff);
        $storeId = $store ? $store->id : null;

        $productsQuery = ProductVariant::with(['product'])
            ->where('active', true)
            ->whereHas('product', function ($q) {
                $q->where('active', true)
                  ->whereNull('deleted_at');
            });

        if ($this->search) {
            $productsQuery->where(function ($q) {
                $q->where('sku', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function ($q2) {
                      $q2->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $products = $productsQuery->simplePaginate(24);
        $categories = \App\Models\Category::where('active', true)->get();

        return view('livewire.pos-screen', [
            'products' => $products,
            'categories' => $categories,
            'storeId' => $storeId,
        ])->layout('layouts.app');
    }
}
