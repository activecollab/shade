<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Bootstrap;

use Symfony\Component\Console\Application;

interface BootstrapApplicationInterface
{
    public function bootstrapApp(): Application;
}
