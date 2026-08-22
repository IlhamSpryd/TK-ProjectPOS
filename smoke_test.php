<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

echo "--- SMOKE TEST ---\n";

$user = User::first();
if (!$user) {
    echo "No user found. Creating a dummy user...\n";
    $userId = Str::uuid()->toString();
    DB::table('users')->insert([
        'id' => $userId,
        'name' => 'Smoke Tester',
        'email' => 'smoke@tester.com',
        'password' => bcrypt('password'),
        'role_id' => null,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $user = User::find($userId);
}
echo "User found: {$user->email}\n";

// Login
Auth::login($user);
echo "Logged in as ID: " . Auth::id() . "\n";

// Find store
$storeId = DB::table('staff_stores')->where('user_id', $user->id)->value('store_id');
if (!$storeId) {
    $storeId = DB::table('stores')->value('id');
    if (!$storeId) {
        $storeId = '9cbeb156-10a6-4331-a1bf-35e226b7e6dc';
        DB::table('stores')->insert([
            'id' => $storeId,
            'name' => 'Smoke Store',
            'address' => 'Dummy',
            'phone' => '123'
        ]);
    }
}
echo "Using Store ID: {$storeId}\n";

// Simulate Middleware
$request = Request::create('/test', 'GET');
$middleware = new \App\Http\Middleware\SetStaffContext();

$response = $middleware->handle($request, function ($req) use ($storeId, $user) {
    echo "Inside Middleware - Trying to insert a sale...\n";
    
    $saleId = Str::uuid()->toString();
    $saleNumber = 'SMOKE-' . time();
    
    try {
        DB::table('sales')->insert([
            'id' => $saleId,
            'store_id' => $storeId,
            'staff_id' => $user->id,
            'sale_date' => now(),
            'sale_number' => $saleNumber,
            'subtotal' => 100,
            'discount_total' => 0,
            'tax_total' => 0,
            'service_charge_total' => 0,
            'grand_total' => 100,
            'payment_status' => 'paid',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "SUCCESS: Sale inserted with ID {$saleId}\n";
        
        DB::table('sales')->where('id', $saleId)->delete();
        echo "SUCCESS: Sale cleaned up.\n";
        
    } catch (\Exception $e) {
        echo "ERROR during insert: " . $e->getMessage() . "\n";
    }
    
    return new \Illuminate\Http\Response('ok');
});

// Terminate
$middleware->terminate($request, $response);
echo "Middleware terminated.\n";
echo "--- SMOKE TEST COMPLETE ---\n";
