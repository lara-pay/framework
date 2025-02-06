<?php

namespace LaraPay\Framework\Commands;

use Illuminate\Console\Command;
use App\Models\Gateway;

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

        $gatewaysTable = config('larapay.tables.gateways', 'larapay_gateways');

        $alias = text(
            label: 'Enter the alias for the gateway',
            validate: ['alias' => ['required', 'string', "unique:{$gatewaysTable},alias"]]
        );

        $config = $gateway->getConfig();

        $configValues = [];

        foreach($config as $key => $value) {
            if($value['type'] == 'select')
            {
                $configValues[$key] = select(
                    label: $value['label'],
                    options: $value['options'],
                    validate: $value['rules'],
                    default: $value['default'] ?? '',
                    hint: $value['description'] ?? ''
                );
            }
            else {
                $configValues[$key] = text(
                    label: $value['label'],
                    validate: $value['rules'],
                    default: $value['default'] ?? '',
                    hint: $value['description'] ?? ''
                );
            }
        }

        $gateway = Gateway::create([
            'alias' => $alias,
            'identifier' => $gateway->getId(),
            'namespace' => $gateways[$selectedGateway],
            'config' => $configValues,
        ]);

        table(
            headers: ['ID', 'Alias', 'Identifier', 'Namespace'],
            rows: [
                [$gateway->id, $gateway->alias, $gateway->identifier, $gateway->namespace]
            ]
        );
    }
}
