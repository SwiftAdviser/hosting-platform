<?php
require __DIR__ . '/../../../../vendor/autoload.php';

$classes = [
    'App\\Services\\AgentDeployerService',
    'App\\Services\\KiloClawClientService',
    'App\\Services\\TelegramBotRegistrarService',
    'App\\Services\\OnChainOSPaymentService',
];

foreach ($classes as $c) {
    echo $c . ': ' . (class_exists($c) ? "ok" : "fail") . "\n";
}
