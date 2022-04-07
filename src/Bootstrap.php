<?php

declare(strict_types=1);

namespace HPT;

use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;

class Bootstrap
{

    public static function boot(): Container
    {
        define('BASE_DIR', realpath(__DIR__ . '/..') . '/');
        define('CONFIG_DIR', BASE_DIR . '/config/');

        $containerBuilder = new ContainerLoader(BASE_DIR . 'temp');

        return new ($containerBuilder->load(static function (Compiler $compiler): void {
            $compiler->loadConfig(CONFIG_DIR . 'config.neon');
        }));
    }
}
