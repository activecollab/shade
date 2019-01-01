<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Renderer;

use ActiveCollab\Shade\Element\ElementInterface;
use ActiveCollab\Shade\ProjectInterface;

interface RendererInterface
{
    public function renderProjectBody(ProjectInterface $project);
    public function renderElementBody(ElementInterface $element);
}
