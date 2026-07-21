<?php

use Illuminate\Support\Facades\Route;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

Route::get('/', function () {
    $sites = Site::all();
    return view('dashboard', compact('sites'));
});

Route::post('/sites', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'domain' => 'required|string|max:255',
    ]);
    
    $data['api_key'] = Str::random(32);
    $site = Site::create($data);
    
    return response()->json(['site' => $site]);
});
