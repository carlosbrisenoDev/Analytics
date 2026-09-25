<?php

use App\Models\Event;
use Carbon\Carbon;

$site = 'ucatem';
$ips = ['192.168.1.1', '192.168.1.2', '10.0.0.5', '172.16.0.4', '8.8.8.8', '1.1.1.1', '192.168.0.100', '10.0.0.12', '127.0.0.1', '200.1.2.3'];

// Generate about 50-80 events
$totalEvents = rand(50, 80);
$now = Carbon::now();

for ($i = 0; $i < $totalEvents; $i++) {
    $ip = $ips[array_rand($ips)];
    
    // Most events are views
    $isClick = rand(1, 100) > 70; // 30% clicks
    $type = $isClick ? 'click' : 'view';
    
    $content = null;
    if ($isClick) {
        $contents = ['CTA', 'CTA submit', 'footer link', 'nav link'];
        $content = $contents[array_rand($contents)];
    } else {
        $contents = ['/article/hola-mundo', '/article/que-es-marketing', '/home', '/about'];
        $content = $contents[array_rand($contents)];
    }

    // Randomize time in the last 2 hours
    $minutesAgo = rand(1, 120);
    $time = (clone $now)->subMinutes($minutesAgo)->addSeconds(rand(0, 59));
    
    Event::create([
        'site_name' => $site,
        'type' => $type,
        'ip_address' => $ip,
        'content' => $content,
        'created_at' => $time,
        'updated_at' => $time,
    ]);
}

echo "Inserted $totalEvents events.\n";
