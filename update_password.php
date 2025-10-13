<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Find admin user and update password
$user = User::where('email', 'admin@vnmt.com')->first();

if ($user) {
    $user->password = Hash::make('admin123');
    $user->save();
    echo "Password updated successfully!\n";
    echo "New login credentials:\n";
    echo "Email: admin@vnmt.com\n";
    echo "Password: admin123\n";
} else {
    echo "Admin user not found!\n";
}