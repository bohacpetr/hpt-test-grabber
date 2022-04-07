#!/bin/env php
<?php

declare(strict_types=1);

use HPT\Bootstrap;
use HPT\Dispatcher;

require_once __DIR__ . '/vendor/autoload.php';

$container = Bootstrap::boot();
$dispatcher = $container->getByType(Dispatcher::class);

echo $dispatcher->run() . PHP_EOL;
