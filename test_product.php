<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = '3667be3c-825a-4508-ae21-8ae367ce6279';
$p = \Illuminate\Support\Facades\DB::table('products')->where('id', $id)->first();
echo "Product Name: {$p->name}\n";
echo "Image URL: {$p->image_url}\n";
