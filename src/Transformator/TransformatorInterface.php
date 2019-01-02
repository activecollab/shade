<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Project\ProjectInterface;

interface TransformatorInterface
{
    public function transform(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        string $markdownContent
    ): string;
}
