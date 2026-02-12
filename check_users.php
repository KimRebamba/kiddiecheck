<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \App\Models\User::all();
echo "Total Users: " . count($users) . "\n";
foreach($users as $u) {
    echo "- {$u->username} ({$u->role})\n";
}
