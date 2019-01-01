<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

use ActiveCollab\Shade\ProjectFactory\ProjectFactory;
use ActiveCollab\Shade\ProjectFactory\ProjectFactoryInterface;
use ActiveCollab\Shade\Renderer\Renderer;
use ActiveCollab\Shade\Renderer\RendererInterface;

return [
    RendererInterface::class => function() {
        return new Renderer();
    },

    ProjectFactoryInterface::class => function()
    {
        return new ProjectFactory();
    },
];
