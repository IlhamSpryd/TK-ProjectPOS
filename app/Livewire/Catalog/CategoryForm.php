<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CategoryForm extends Component
{
    public ?string $categoryId = null;
    public string $name = '';
    public ?string $parent_id = null;
    public bool $active = true;

    protected $listeners = ['editCategory' => 'loadCategory'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|string|exists:categories,id',
        'active' => 'boolean',
    ];

    public function loadCategory($id = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'parent_id', 'active']);
        
        $this->categoryId = $id;

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $this->name = $category->name;
            $this->parent_id = $category->parent_id;
            $this->active = $category->active;
        }

        // Trigger Alpine to open the Flux modal
        $this->dispatch('open-category-modal');
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->categoryId) {
                $category = Category::findOrFail($this->categoryId);
                $category->update([
                    'name' => $this->name,
                    'parent_id' => $this->parent_id,
                    'active' => $this->active,
                ]);
                $message = 'Kategori berhasil diperbarui.';
            } else {
                Category::create([
                    'id' => Str::uuid()->toString(),
                    'name' => $this->name,
                    'parent_id' => $this->parent_id,
                    'active' => $this->active,
                ]);
                $message = 'Kategori berhasil ditambahkan.';
            }

            $this->dispatch('categorySaved');
            $this->dispatch('close-category-modal');
            $this->dispatch('toast', message: $message, type: 'success');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan kategori: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Gagal menyimpan kategori.', type: 'error');
        }
    }

    public function render()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->when($this->categoryId, function ($query) {
                $query->where('id', '!=', $this->categoryId); // Prevent self-parenting
            })
            ->orderBy('name')
            ->get();

        return view('livewire.catalog.category-form', [
            'parentCategories' => $parentCategories
        ]);
    }
}
