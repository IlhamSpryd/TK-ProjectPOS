<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

putenv('DB_CONNECTION=sqlite');
config(['database.default' => 'sqlite']);
config(['database.connections.sqlite.database' => database_path('database.sqlite')]);

$db = \Illuminate\Support\Facades\DB::connection('sqlite');

try {
    $products = $db->table('products')->get();
    foreach ($products as $p) {
        echo "Product ID: {$p->id}\n";
        echo "Product Name: {$p->name}\n";
        echo "Image URL: " . ($p->image_url ?? 'NULL') . "\n";
        echo "-----------\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
