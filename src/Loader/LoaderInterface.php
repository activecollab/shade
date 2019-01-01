<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Loader;

use ActiveCollab\Shade\Ability\LoadableInterface;
use ActiveCollab\Shade\Loader\Result\LoaderResultInterface;

interface LoaderInterface
{
    public function load(LoadableInterface $loadableElement): LoaderResultInterface;
}
