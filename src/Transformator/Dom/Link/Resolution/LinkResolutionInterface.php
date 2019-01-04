<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator\Dom\Link\Resolution;

use ActiveCollab\Shade\Ability\BuildableInterface;

interface LinkResolutionInterface
{
    public function isFound(): bool;
    public function getTarget(): ?BuildableInterface;
    public function getNotFoundMessage(): string;
}
