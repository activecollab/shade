<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Project\ProjectInterface;

interface ElementInterface extends BuildableInterface
{
    public function getProject(): ProjectInterface;
    public function getPath(): string;
    public function getShortName(): string;
    public function getSlug(): string;
    public function getTitle(): string;
}
