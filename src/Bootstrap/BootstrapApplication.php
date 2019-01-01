<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Bootstrap;

use DI\ContainerBuilder;
use DirectoryIterator;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\Console\Application;

class BootstrapApplication implements BootstrapApplicationInterface
{
    private $application_version;
    private $application_path;
    private $debug;

    public function __construct(
        string $application_version,
        string $application_path,
        bool $debug = false
    )
    {
        $this->application_version = $application_version;
        $this->application_path = $application_path;
        $this->debug = $debug;
    }

    public function bootstrapApp(): Application
    {
        if ($this->debug) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
        }

        $container = $this->getContainer();
        $application = $this->getApplication();

        foreach (new DirectoryIterator($this->application_path . '/src/Command') as $file) {
            if ($file->isFile()) {
                $class_name = ('\\ActiveCollab\\Shade\\Command\\' . $file->getBasename('.php'));

                if (!(new ReflectionClass($class_name))->isAbstract()) {
                    $application->add(new $class_name($container));
                }
            }
        }

        return $application;
    }

    private function getContainer(): ContainerInterface
    {
        return (new ContainerBuilder())
            ->addDefinitions($this->application_path . '/src/dependencies.php')
            ->build();
    }

    private function getApplication(): Application
    {
        return new Application('Shade', $this->application_version);
    }
}
