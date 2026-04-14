<?php
require __DIR__ . '/../../../../vendor/autoload.php';

$symbols = [
    'App\\Services\\AgentDeployerService',
    'App\\Services\\KiloClawClientService',
    'App\\Services\\OnChainOSPaymentService',
    'App\\Services\\TelegramBotRegistrarService',
    'App\\Services\\Telegram\\TelegramHttpClient',
    'App\\Services\\Telegram\\TelegramTransportException',
    'App\\Services\\OnChainOS\\OnChainOSClient',
    'App\\Services\\OnChainOS\\OnChainOSException',
];

$failed = 0;
foreach ($symbols as $symbol) {
    if (class_exists($symbol) || interface_exists($symbol)) {
        echo "ok: $symbol\n";
    } else {
        echo "MISSING: $symbol\n";
        $failed++;
    }
}

exit($failed === 0 ? 0 : 1);
