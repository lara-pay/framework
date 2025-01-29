<?php

namespace LaraPay\Framework\Commands;

use Illuminate\Console\Command;
use LaraPay\Framework\Foundation\Gateway;

use function Laravel\Prompts\{select, text, table};

class SetupGatewayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:setup {gateway?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a gateway';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $gateways = Gateway::getInstalledGateways();

        if(empty($gateways)) {
            $this->error('No gateways installed');
            return;
        }

        if($this->argument('gateway')) {
            $selectedGateway = $this->argument('gateway');
        } else {
            $selectedGateway = select(
                label: 'Select the gateway you want to setup',
                options: array_keys($gateways)
            );
        }

        if(!array_key_exists($selectedGateway, $gateways)) {
            $this->error("{$selectedGateway} is not installed");
            return;
        }

        $gateway = (new $gateways[$selectedGateway]);

        $alias = text(
            label: 'Enter the alias for the gateway',
            validate: ['alias' => ['required', 'string', 'unique:larapay_gateways,alias']]
        );

        $config = $gateway->getConfig();

        $configValues = [];

        foreach($config as $key => $value) {
            $configValues[$key] = text(
                label: $value['label'],
                validate: $value['rules']
            );
        }

        $gateway = Gateway::create([
            'alias' => $alias,
            'identifier' => $gateway->getId(),
            'namespace' => $gateways[$selectedGateway],
            'config' => encrypt($configValues),
        ]);

        table(
            headers: ['ID', 'Alias', 'Identifier', 'Namespace'],
            rows: [
                [$gateway->id, $gateway->alias, $gateway->identifier, $gateway->namespace]
            ]
        );
    }
}
