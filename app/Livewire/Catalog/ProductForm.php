<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?string $productId = null;

    // Product fields
    public string $name = '';

    public string $sku = '';

    public ?string $category_id = null;

    public string $description = '';

    public string $unit = 'pcs';

    public bool $track_stock = true;

    public bool $is_service = false;

    public bool $active = true;

    public $image;

    public ?string $existingImage = null;

    // Additional Product specs
    public array $product_attributes = [];
    public array $product_attributes_list = [];

    // Variants array
    public array $variants = [];

    public function mount($id = null): void
    {
        $this->productId = $id;

        if ($this->productId) {
            $product = Product::with('variants')->findOrFail($this->productId);
            $this->name = $product->name;
            $this->sku = $product->sku ?? '';
            $this->category_id = $product->category_id;
            $this->description = $product->description ?? '';
            $this->unit = $product->unit ?? 'pcs';
            $this->track_stock = $product->track_stock;
            $this->is_service = $product->is_service;
            $this->active = $product->active;
            $this->existingImage = $product->image_url;
            $this->product_attributes = $product->attributes ?? [];
            foreach ($this->product_attributes as $key => $value) {
                $this->product_attributes_list[] = ['key' => $key, 'value' => $value];
            }

            foreach ($product->variants as $variant) {
                $varAttrList = [];
                $varAttrs = $variant->attributes ?? [];
                foreach ($varAttrs as $k => $v) {
                    $varAttrList[] = ['key' => $k, 'value' => $v];
                }

                $this->variants[] = [
                    'id' => $variant->id,
                    'sku' => $variant->sku ?? '',
                    'barcode' => $variant->barcode ?? '',
                    'cost_price' => $variant->cost_price ?? 0,
                    'selling_price' => $variant->selling_price ?? 0,
                    'attributes' => $varAttrs,
                    'attributes_list' => $varAttrList,
                    'active' => $variant->active,
                    'is_deleted' => false,
                ];
            }
        } else {
            // Default: 1 varian kosong untuk produk baru
            $this->addVariant();
        }
    }

    public function addVariant(): void
    {
        $this->variants[] = [
            'id' => null,
            'sku' => '',
            'barcode' => '',
            'cost_price' => 0,
            'selling_price' => 0,
            'attributes' => [],
            'attributes_list' => [],
            'active' => true,
            'is_deleted' => false,
        ];
    }

    public function removeVariant(int $index): void
    {
        if (isset($this->variants[$index]['id']) && $this->variants[$index]['id'] !== null) {
            $this->variants[$index]['is_deleted'] = true;
        } else {
            unset($this->variants[$index]);
            $this->variants = array_values($this->variants);
        }
    }

    public function addProductAttribute(): void
    {
        $this->product_attributes_list[] = ['key' => '', 'value' => ''];
    }

    public function removeProductAttribute(int $index): void
    {
        unset($this->product_attributes_list[$index]);
        $this->product_attributes_list = array_values($this->product_attributes_list);
    }

    public function addVariantAttribute(int $variantIndex): void
    {
        $this->variants[$variantIndex]['attributes_list'][] = ['key' => '', 'value' => ''];
    }

    public function removeVariantAttribute(int $variantIndex, int $attrIndex): void
    {
        unset($this->variants[$variantIndex]['attributes_list'][$attrIndex]);
        $this->variants[$variantIndex]['attributes_list'] = array_values($this->variants[$variantIndex]['attributes_list']);
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'category_id' => 'nullable|string|exists:categories,id',
            'unit' => 'nullable|string|max:50',
            'image' => 'nullable|image|max:2048',
            'variants.*.selling_price' => 'required|numeric|min:0',
            'variants.*.cost_price' => 'nullable|numeric|min:0',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $activeVariantsCount = collect($this->variants)->where('is_deleted', false)->count();
        if ($activeVariantsCount === 0) {
            $this->dispatch('toast', message: 'Minimal harus ada 1 varian produk.', type: 'error');

            return;
        }

        try {
            DB::transaction(function () {
                $imagePath = $this->image
                    ? $this->image->store('products/' . auth()->user()->tenant_id, 'public')
                    : null;

                // Format attributes from lists back to associative arrays
                $this->product_attributes = [];
                foreach ($this->product_attributes_list as $attr) {
                    if (trim($attr['key']) !== '') {
                        $this->product_attributes[trim($attr['key'])] = trim($attr['value']);
                    }
                }

                if ($this->productId) {
                    $product = Product::findOrFail($this->productId);

                    $updateData = [
                        'name' => $this->name,
                        'sku' => $this->sku ?: null,
                        'category_id' => $this->category_id ?: null,
                        'description' => $this->description,
                        'unit' => $this->unit,
                        'track_stock' => $this->track_stock,
                        'is_service' => $this->is_service,
                        'active' => $this->active,
                        'attributes' => $this->product_attributes,
                    ];

                    if ($imagePath) {
                        $updateData['image_url'] = $imagePath;
                    }

                    $product->update($updateData);
                } else {
                    $product = Product::create([
                        'id' => Str::uuid()->toString(),
                        'name' => $this->name,
                        'sku' => $this->sku ?: null,
                        'category_id' => $this->category_id ?: null,
                        'description' => $this->description,
                        'unit' => $this->unit,
                        'track_stock' => $this->track_stock,
                        'is_service' => $this->is_service,
                        'active' => $this->active,
                        'attributes' => $this->product_attributes,
                        'image_url' => $imagePath,
                    ]);

                    $this->productId = $product->id;
                }

                // Sync varian
                foreach ($this->variants as $variantData) {
                    if ($variantData['is_deleted']) {
                        if ($variantData['id']) {
                            ProductVariant::where('id', $variantData['id'])->delete();
                        }

                        continue;
                    }

                    // Rebuild variant attributes associative array
                    $varAttrs = [];
                    if (isset($variantData['attributes_list'])) {
                        foreach ($variantData['attributes_list'] as $attr) {
                            if (trim($attr['key']) !== '') {
                                $varAttrs[trim($attr['key'])] = trim($attr['value']);
                            }
                        }
                    }

                    if ($variantData['id']) {
                        $pv = ProductVariant::find($variantData['id']);
                        if ($pv) {
                            $pv->update([
                                'sku' => $variantData['sku'] ?: null,
                                'barcode' => $variantData['barcode'] ?: null,
                                'cost_price' => $variantData['cost_price'] ?: 0,
                                'selling_price' => $variantData['selling_price'] ?: 0,
                                'attributes' => $varAttrs,
                                'active' => $variantData['active'],
                            ]);
                        }
                    } else {
                        ProductVariant::create([
                            'id' => Str::uuid()->toString(),
                            'product_id' => $product->id,
                            'sku' => $variantData['sku'] ?: null,
                            'barcode' => $variantData['barcode'] ?: null,
                            'cost_price' => $variantData['cost_price'] ?: 0,
                            'selling_price' => $variantData['selling_price'] ?: 0,
                            'attributes' => $varAttrs,
                            'active' => $variantData['active'],
                        ]);
                    }
                }
            });

            $this->dispatch('toast', message: 'Produk berhasil disimpan.', type: 'success');
            $this->redirectRoute('catalog.products', navigate: true);

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan produk: '.$e->getMessage());
            $this->dispatch('toast', message: 'Gagal menyimpan produk: '.$e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.catalog.product-form', [
            'categories' => Category::activeCached(),
        ])
            ->layout('components.layouts.app', [
                'title' => $this->productId ? 'Edit Produk' : 'Tambah Produk Baru',
                'breadcrumbs' => [
                    ['label' => 'Dashboard', 'route' => route('dashboard')],
                    ['label' => 'Katalog Produk', 'route' => route('catalog.products')],
                    ['label' => $this->productId ? 'Edit' : 'Tambah'],
                ],
                'actions' => new HtmlString(Blade::render(
                    '<x-ui.button variant="ghost" href="{{ route(\'catalog.products\') }}" wire:navigate class="px-5">Batal</x-ui.button>
                     <x-ui.button variant="primary" x-data x-on:click="$dispatch(\'trigger-save-product\')" class="px-6 shadow-xs">Simpan Produk</x-ui.button>'
                )),
            ]);
    }
}
