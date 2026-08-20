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

relations = {
    'Staff': [
        "public function role() { return $this->belongsTo(Role::class); }",
        "public function stores() { return $this->belongsToMany(Store::class, 'staff_stores')->withPivot('is_primary'); }"
    ],
    'Store': [
        "public function staff() { return $this->belongsToMany(Staff::class, 'staff_stores')->withPivot('is_primary'); }"
    ],
    'Product': [
        "public function category() { return $this->belongsTo(Category::class); }",
        "public function supplier() { return $this->belongsTo(Supplier::class); }",
        "public function taxCategory() { return $this->belongsTo(TaxCategory::class); }",
        "public function variants() { return $this->hasMany(ProductVariant::class, 'product_id'); }"
    ],
    'ProductVariant': [
        "public function product() { return $this->belongsTo(Product::class); }"
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
    'Sale': [
        "public function store() { return $this->belongsTo(Store::class); }",
        "public function customer() { return $this->belongsTo(Customer::class); }",
        "public function staff() { return $this->belongsTo(Staff::class); }",
        "public function register() { return $this->belongsTo(Register::class); }",
        "public function shift() { return $this->belongsTo(Shift::class); }",
        "public function items() { return $this->hasMany(SaleItem::class); }",
        "public function payments() { return $this->hasMany(Payment::class); }"
    ],
    'SaleItem': [
        "public function sale() { return $this->belongsTo(Sale::class); }",
        "public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }",
        "public function taxCategory() { return $this->belongsTo(TaxCategory::class); }",
        "public function discount() { return $this->belongsTo(Discount::class); }"
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
    ],
    'Discount': [
        "public function store() { return $this->belongsTo(Store::class); }"
    ],
    'Shift': [
        "public function register() { return $this->belongsTo(Register::class); }",
        "public function staff() { return $this->belongsTo(Staff::class); }"
    ],
    'Register': [
        "public function store() { return $this->belongsTo(Store::class); }"
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
    
    has_updated_at = False
    
    casts.append("'id' => 'string'")
    
    for col in columns:
        col_name = col['name']
        if col_name == 'CONSTRAINT':
            continue
            
        if col_name not in ['id', 'created_at', 'updated_at', 'deleted_at']:
            fillable.append(col_name)
            
        col_def = col['def'].lower()
        
        if col_name == 'updated_at':
            has_updated_at = True
            
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
                
    php_code = "<?php\n\nnamespace App\\Models;\n\n"
    php_code += r"use Illuminate\Database\Eloquent\Factories\HasFactory;" + "\n"
    
    if model_name == 'Staff':
        php_code += r"use Illuminate\Foundation\Auth\User as Authenticatable;" + "\n"
        php_code += r"use Illuminate\Notifications\Notifiable;" + "\n"
    else:
        php_code += r"use Illuminate\Database\Eloquent\Model;" + "\n"
        
    has_soft_deletes = model_name in ['Product', 'ProductVariant']
    if has_soft_deletes:
        php_code += r"use Illuminate\Database\Eloquent\SoftDeletes;" + "\n"
        
    if model_name == 'Staff':
        php_code += f"\nclass {model_name} extends Authenticatable\n{{\n"
        php_code += f"    use HasFactory, Notifiable;\n"
    else:
        php_code += f"\nclass {model_name} extends Model\n{{\n"
        php_code += f"    use HasFactory;\n"
    
    if has_soft_deletes:
        php_code += f"    use SoftDeletes;\n"
        
    php_code += f"\n    protected $table = '{table_name}';\n"
    php_code += f"    protected $keyType = 'string';\n"
    php_code += f"    public $incrementing = false;\n"
    if not has_updated_at:
        php_code += f"    public $timestamps = false;\n"
    php_code += "\n"
    
    fillable_str = ",\n        ".join(f"'{f}'" for f in fillable)
    php_code += f"    protected $fillable = [\n        {fillable_str}\n    ];\n\n"
    
    if model_name == 'Staff':
        php_code += "    protected $hidden = [\n        'password_hash',\n        'pin_hash',\n        'remember_token',\n    ];\n\n"
        
    casts_str = ",\n        ".join(casts)
    php_code += f"    protected $casts = [\n        {casts_str}\n    ];\n\n"
    
    if model_name == 'Staff':
        php_code += "    public function getAuthPassword() {\n        return $this->password_hash;\n    }\n\n"
    
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
