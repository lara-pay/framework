<?php

namespace LaraPay\Framework\Commands;

use Illuminate\Console\Command;
use App\Models\Gateway;

class ListGatewayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists gateway configurations';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->table(
            ['Id', 'Alias', 'Identifier', 'Namespace'],
            Gateway::all(['id', 'alias', 'identifier', 'namespace'])->toArray()
        );
    }
}
