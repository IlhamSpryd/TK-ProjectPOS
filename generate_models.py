import json
import os

model_mapping = {
    'Role': 'roles',
    'Staff': 'staff',
    'Store': 'stores',
    'TaxCategory': 'tax_categories',
    'Category': 'categories',
    'Supplier': 'suppliers',
    'Product': 'products',
    'ProductVariant': 'product_variants',
    'InventoryStock': 'inventory_stock',
    'InventoryMovement': 'inventory_movements',
    'Customer': 'customers',
    'PurchaseOrder': 'purchase_orders',
    'PurchaseOrderItem': 'purchase_order_items',
    'Discount': 'discounts',
    'Register': 'registers',
    'Shift': 'shifts',
    'Sale': 'sales',
    'SaleItem': 'sale_items',
    'Payment': 'payments',
    'SaleReturn': 'sale_returns',
    'SaleReturnItem': 'sale_return_items',
    'Expense': 'expenses'
}

# Define relations manually based on implementation plan for clarity and accuracy
relations = {
    'Role': [
        "public function staff() { return $this->hasMany(Staff::class); }"
    ],
    'Staff': [
        "public function role() { return $this->belongsTo(Role::class); }",
        "public function expenses() { return $this->hasMany(Expense::class); }",
        "public function sales() { return $this->hasMany(Sale::class); }",
        "public function shifts() { return $this->hasMany(Shift::class); }",
        "public function saleReturns() { return $this->hasMany(SaleReturn::class); }",
        "public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }",
        "public function inventoryMovements() { return $this->hasMany(InventoryMovement::class); }"
    ],
    'Store': [
        "public function taxCategory() { return $this->belongsTo(TaxCategory::class, 'default_tax_category_id'); }",
        "public function inventoryStocks() { return $this->hasMany(InventoryStock::class); }",
        "public function inventoryMovements() { return $this->hasMany(InventoryMovement::class); }",
        "public function discounts() { return $this->hasMany(Discount::class); }",
        "public function expenses() { return $this->hasMany(Expense::class); }",
        "public function registers() { return $this->hasMany(Register::class); }",
        "public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }",
        "public function sales() { return $this->hasMany(Sale::class); }"
    ],
    'TaxCategory': [
        "public function stores() { return $this->hasMany(Store::class, 'default_tax_category_id'); }",
        "public function products() { return $this->hasMany(Product::class); }",
        "public function saleItems() { return $this->hasMany(SaleItem::class); }"
    ],
    'Category': [
        "public function parent() { return $this->belongsTo(Category::class, 'parent_id'); }",
        "public function children() { return $this->hasMany(Category::class, 'parent_id'); }",
        "public function products() { return $this->hasMany(Product::class); }"
    ],
    'Supplier': [
        "public function products() { return $this->hasMany(Product::class); }",
        "public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class); }"
    ],
    'Product': [
        "public function category() { return $this->belongsTo(Category::class); }",
        "public function supplier() { return $this->belongsTo(Supplier::class); }",
        "public function taxCategory() { return $this->belongsTo(TaxCategory::class); }",
        "public function variants() { return $this->hasMany(ProductVariant::class); }"
    ],
    'ProductVariant': [
        "public function product() { return $this->belongsTo(Product::class); }",
        "public function inventoryStocks() { return $this->hasMany(InventoryStock::class, 'variant_id'); }",
        "public function inventoryMovements() { return $this->hasMany(InventoryMovement::class, 'variant_id'); }",
        "public function purchaseOrderItems() { return $this->hasMany(PurchaseOrderItem::class, 'variant_id'); }",
        "public function saleItems() { return $this->hasMany(SaleItem::class, 'variant_id'); }"
    ],
    'InventoryStock': [
        "public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }",
        "public function store() { return $this->belongsTo(Store::class); }"
    ],
    'InventoryMovement': [
        "public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }",
        "public function store() { return $this->belongsTo(Store::class); }",
        "public function staff() { return $this->belongsTo(Staff::class); }"
    ],
    'Customer': [
        "public function sales() { return $this->hasMany(Sale::class); }"
    ],
    'PurchaseOrder': [
        "public function store() { return $this->belongsTo(Store::class); }",
        "public function supplier() { return $this->belongsTo(Supplier::class); }",
        "public function staff() { return $this->belongsTo(Staff::class); }",
        "public function items() { return $this->hasMany(PurchaseOrderItem::class); }"
    ],
    'PurchaseOrderItem': [
        "public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }",
        "public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }"
    ],
    'Discount': [
        "public function store() { return $this->belongsTo(Store::class); }"
    ],
    'Register': [
        "public function store() { return $this->belongsTo(Store::class); }",
        "public function shifts() { return $this->hasMany(Shift::class); }",
        "public function sales() { return $this->hasMany(Sale::class); }"
    ],
    'Shift': [
        "public function register() { return $this->belongsTo(Register::class); }",
        "public function staff() { return $this->belongsTo(Staff::class); }",
        "public function sales() { return $this->hasMany(Sale::class); }"
    ],
    'Sale': [
        "public function store() { return $this->belongsTo(Store::class); }",
        "public function customer() { return $this->belongsTo(Customer::class); }",
        "public function staff() { return $this->belongsTo(Staff::class); }",
        "public function register() { return $this->belongsTo(Register::class); }",
        "public function shift() { return $this->belongsTo(Shift::class); }",
        "public function voidedBy() { return $this->belongsTo(Staff::class, 'voided_by'); }",
        "public function items() { return $this->hasMany(SaleItem::class); }",
        "public function payments() { return $this->hasMany(Payment::class); }",
        "public function saleReturns() { return $this->hasMany(SaleReturn::class); }"
    ],
    'SaleItem': [
        "public function sale() { return $this->belongsTo(Sale::class); }",
        "public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }",
        "public function taxCategory() { return $this->belongsTo(TaxCategory::class); }",
        "public function returnItems() { return $this->hasMany(SaleReturnItem::class); }"
    ],
    'Payment': [
        "public function sale() { return $this->belongsTo(Sale::class); }"
    ],
    'SaleReturn': [
        "public function sale() { return $this->belongsTo(Sale::class); }",
        "public function staff() { return $this->belongsTo(Staff::class); }",
        "public function items() { return $this->hasMany(SaleReturnItem::class); }"
    ],
    'SaleReturnItem': [
        "public function saleReturn() { return $this->belongsTo(SaleReturn::class); }",
        "public function saleItem() { return $this->belongsTo(SaleItem::class); }"
    ],
    'Expense': [
        "public function store() { return $this->belongsTo(Store::class); }",
        "public function staff() { return $this->belongsTo(Staff::class); }"
    ]
}

def load_schema():
    with open('schema_summary.json', 'r') as f:
        return json.load(f)

def generate_model_class(model_name, table_name, schema):
    table_data = schema.get(table_name, {"columns": []})
    columns = table_data["columns"]
    
    fillable = []
    casts = []
    has_soft_deletes = False
    
    casts.append("'id' => 'string'")
    
    for col in columns:
        col_name = col['name']
        if col_name == 'CONSTRAINT':
            continue
            
        if col_name not in ['id', 'created_at', 'updated_at', 'deleted_at']:
            fillable.append(col_name)
            
        col_def = col['def'].lower()
        
        if col_name == 'deleted_at':
            has_soft_deletes = True
            
        # Determine casts
        if 'boolean' in col_def:
            casts.append(f"'{col_name}' => 'boolean'")
        elif 'jsonb' in col_def:
            casts.append(f"'{col_name}' => 'array'")
        elif 'numeric' in col_def or 'decimal' in col_def:
            casts.append(f"'{col_name}' => 'decimal:2'")
        elif 'timestamp' in col_def:
            if col_name not in ['created_at', 'updated_at', 'deleted_at']:
                casts.append(f"'{col_name}' => 'datetime'")
                
    php_code = f"<?php\n\nnamespace App\Models;\n\n"
    php_code += "use Illuminate\Database\Eloquent\Factories\HasFactory;\n"
    php_code += "use Illuminate\Database\Eloquent\Model;\n"
    
    if has_soft_deletes:
        php_code += "use Illuminate\Database\Eloquent\SoftDeletes;\n"
        
    php_code += f"\nclass {model_name} extends Model\n{{\n"
    php_code += f"    use HasFactory;\n"
    
    if has_soft_deletes:
        php_code += f"    use SoftDeletes;\n"
        
    php_code += f"\n    protected $table = '{table_name}';\n"
    php_code += f"    protected $keyType = 'string';\n"
    php_code += f"    public $incrementing = false;\n\n"
    
    fillable_str = ",\n        ".join(f"'{f}'" for f in fillable)
    php_code += f"    protected $fillable = [\n        {fillable_str}\n    ];\n\n"
    
    casts_str = ",\n        ".join(casts)
    php_code += f"    protected $casts = [\n        {casts_str}\n    ];\n\n"
    
    if model_name in relations:
        for rel in relations[model_name]:
            php_code += f"    {rel}\n"
            
    php_code += "}\n"
    
    return php_code

def main():
    schema = load_schema()
    models_dir = 'app/Models'
    
    if not os.path.exists(models_dir):
        os.makedirs(models_dir)
        
    for model_name, table_name in model_mapping.items():
        code = generate_model_class(model_name, table_name, schema)
        filepath = os.path.join(models_dir, f"{model_name}.php")
        with open(filepath, 'w') as f:
            f.write(code)
        print(f"Generated {filepath}")

if __name__ == '__main__':
    main()
