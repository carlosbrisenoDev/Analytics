<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site;
use Illuminate\Support\Str;

class AddSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:add {name} {domain} {--key=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new site to the analytics dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');
        $apiKey = $this->option('key') ?: Str::random(32);

        $site = Site::create([
            'name' => $name,
            'domain' => $domain,
            'api_key' => $apiKey,
        ]);

        $this->info("Site '{$site->name}' created successfully!");
        $this->info("API Key: {$site->api_key}");
        
        return Command::SUCCESS;
    }
}
