<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

date_default_timezone_set('UTC');

define('SHADE_ROOT_PATH', dirname(__DIR__));

require SHADE_ROOT_PATH . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application('Shade', file_get_contents(SHADE_ROOT_PATH . '/VERSION'));

foreach (new DirectoryIterator(SHADE_ROOT_PATH . '/src/ActiveCollab/Shade/Command') as $file) {
    if ($file->isFile()) {
        $class_name = ('\\ActiveCollab\\Shade\\Command\\' . $file->getBasename('.php'));

        if (!(new ReflectionClass($class_name))->isAbstract()) {
            $application->add(new $class_name);
        }
    }
}

$application->run();
