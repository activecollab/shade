<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Bootstrap;

use DirectoryIterator;
use ReflectionClass;
use Symfony\Component\Console\Application;

class BootstrapApplication implements BootstrapApplicationInterface
{
    private $application_version;
    private $commands_path;
    private $debug;

    public function __construct($application_version, $commands_path, $debug = false)
    {
        $this->application_version = $application_version;
        $this->commands_path = $commands_path;
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

        $application = new Application('Shade', $this->application_version);

        foreach (new DirectoryIterator($this->commands_path) as $file) {
            if ($file->isFile()) {
                $class_name = ('\\ActiveCollab\\Shade\\Command\\' . $file->getBasename('.php'));

                if (!(new ReflectionClass($class_name))->isAbstract()) {
                    $application->add(new $class_name);
                }
            }
        }

        return $application;
    }
}
