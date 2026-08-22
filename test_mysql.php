<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

putenv('DB_CONNECTION=mysql');
config(['database.default' => 'mysql']);
// Assuming Laragon defaults: root, no password, db tk_project_pos
config(['database.connections.mysql.database' => 'tk_project_pos']);
config(['database.connections.mysql.username' => 'root']);
config(['database.connections.mysql.password' => '']);

try {
    $db = \Illuminate\Support\Facades\DB::connection('mysql');
    $products = $db->table('products')->get();
    echo "Total MySQL products: " . count($products) . "\n";
    foreach ($products as $p) {
        echo "Product ID: {$p->id}\n";
        echo "Product Name: {$p->name}\n";
        echo "Image URL: " . ($p->image_url ?? 'NULL') . "\n";
        echo "-----------\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
