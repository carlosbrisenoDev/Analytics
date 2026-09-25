<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/dashboard', 'GET');
$user = App\Models\User::where('email', 'admin@example.com')->first();
Auth::login($user);
$response = $kernel->handle($request);
file_put_contents('test_dashboard.html', $response->getContent());
