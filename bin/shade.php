<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

use ActiveCollab\Shade\Bootstrap\BootstrapApplication;

date_default_timezone_set('UTC');

define('SHADE_ROOT_PATH', dirname(__DIR__));

require SHADE_ROOT_PATH . '/vendor/autoload.php';

(new BootstrapApplication(
    trim(file_get_contents(SHADE_ROOT_PATH . '/VERSION')),
    SHADE_ROOT_PATH . '/src/ActiveCollab/Shade/Command'
))
    ->bootstrapApp()
        ->run();
