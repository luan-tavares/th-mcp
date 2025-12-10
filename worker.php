<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
/** @var Illuminate\Foundation\Application $app */
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

return $app->makeWith(\App\Handlers\LaravelQueueHandler::class, [
    'connection' => "sqs"
]);
