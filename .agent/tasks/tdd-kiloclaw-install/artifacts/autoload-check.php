<?php
declare(strict_types=1);

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
    'App\\Services\\KiloClaw\\KiloClawHttpClient',
    'App\\Services\\KiloClaw\\KiloClawException',
];

$exit = 0;
foreach ($symbols as $symbol) {
    if (class_exists($symbol) || interface_exists($symbol)) {
        echo $symbol . ": ok\n";
    } else {
        echo $symbol . ": MISSING\n";
        $exit = 1;
    }
}
exit($exit);
