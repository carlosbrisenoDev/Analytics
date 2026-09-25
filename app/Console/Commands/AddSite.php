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
    protected $signature = 'site:add {name} {domain}';

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

        $site = Site::create([
            'name' => $name,
            'domain' => $domain,
        ]);

        $this->info("Site '{$site->name}' created successfully!");
        
        return Command::SUCCESS;
    }
}
