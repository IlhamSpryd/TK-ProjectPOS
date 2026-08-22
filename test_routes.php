<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\Staff::where('email', 'ilhamsepriyadi8@gmail.com')->first();
if (!$user) {
    $user = \App\Models\Staff::first();
}
echo "Testing as user: " . ($user->email ?? 'none') . "\n";

foreach (['/pos', '/katalog/produk', '/staff', '/pelanggan', '/cabang', '/laporan'] as $uri) {
    $request = \Illuminate\Http\Request::create($uri, 'GET');
    $app['auth']->guard()->setUser($user);
    $app['auth']->shouldUse('web');
    
    $response = $kernel->handle($request);
    echo str_pad($uri, 20) . " => HTTP " . $response->getStatusCode() . "\n";
    $kernel->terminate($request, $response);
}
