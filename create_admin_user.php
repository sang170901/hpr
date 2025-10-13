<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Delete existing admin user
User::where('email', 'admin@vnmt.com')->delete();

// Create new admin user
$user = User::create([
    'name' => 'Admin',
    'email' => 'admin@vnmt.com',
    'password' => Hash::make('admin123'),
    'email_verified_at' => now()
]);

echo "New admin user created successfully!\n";
echo "Email: admin@vnmt.com\n";
echo "Password: admin123\n";
echo "User ID: " . $user->id . "\n";