<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/models/UserModel.php';

$users = new UserModel();

$tests = [
    ['farmer1@cropinsurance.ph', 'Password@123'],
    ['admin@cropinsurance.ph',   'Password@123'],
];

foreach ($tests as [$email, $pass]) {
    $user = $users->findByEmail($email);
    if (!$user) { echo "$email → NOT FOUND\n"; continue; }
    $ok = $users->verifyPassword($pass, $user['password']);
    echo "$email → " . ($ok ? "✓ LOGIN OK" : "✗ WRONG PASSWORD") . "\n";
    if ($ok) {
        $token = jwtEncode(['id'=>$user['id'],'email'=>$user['email'],'role'=>$user['role']]);
        echo "  Token: " . substr($token, 0, 40) . "...\n";
    }
}
