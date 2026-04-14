<?php
require 'vendor/autoload.php';
foreach ([
    'App\\Services\\AgentDeployerService',
    'App\\Services\\KiloClawClientService',
    'App\\Services\\OnChainOSPaymentService',
    'App\\Services\\TelegramBotRegistrarService',
    'App\\Services\\Telegram\\TelegramHttpClient',
    'App\\Services\\Telegram\\TelegramTransportException',
] as $class) {
    $exists = interface_exists($class) || class_exists($class);
    echo ($exists ? 'ok' : 'FAIL').": $class\n";
}
