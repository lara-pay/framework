<?php

namespace LaraPay\Framework\Commands;

use Illuminate\Console\Command;
use LaraPay\Framework\Gateway;

use function Laravel\Prompts\{select, text, table};

class UpdateGatewayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:update {gateway?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a gateway';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $gateway = Gateway::where('id', $this->argument('gateway'))->orWhere('alias', $this->argument('gateway'))->first();

        if(!$gateway) {
            $this->error("Gateway {$this->argument('gateway')} by alias or ID not found");
            return;
        }

        $gatewaysTable = config('larapay.tables.gateways', 'larapay_gateways');

        $alias = text(
            label: 'Enter the alias for the gateway',
            validate: ['alias' => ['required', 'string', "unique:{$gatewaysTable},alias,{$gateway->id}"]],
            default: $gateway->alias
        );

        $gatewayInstance = (new $gateway->namespace);
        $config = $gatewayInstance->getConfig();

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

        $gateway->update([
            'alias' => $alias,
            'config' => $configValues
        ]);

        table(
            headers: ['ID', 'Alias', 'Identifier', 'Namespace'],
            rows: [
                [$gateway->id, $gateway->alias, $gateway->identifier, $gateway->namespace]
            ]
        );
    }
}
