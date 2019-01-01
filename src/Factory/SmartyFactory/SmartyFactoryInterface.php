<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Factory\SmartyFactory;

use ActiveCollab\Shade\Plugin\PluginInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\ThemeInterface;
use Smarty;

interface SmartyFactoryInterface
{
    public function createSmarty(
        ProjectInterface $project,
        ThemeInterface $theme,
        PluginInterface ...$plugins
    ): Smarty;
}
