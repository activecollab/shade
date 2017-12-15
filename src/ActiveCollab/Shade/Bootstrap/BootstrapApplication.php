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

    public function __construct($application_version, $commands_path)
    {
        $this->application_version = $application_version;
        $this->commands_path = $commands_path;
    }

    public function bootstrapApp(): Application
    {
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
