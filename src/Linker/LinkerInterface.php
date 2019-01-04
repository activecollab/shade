<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Linker;

use ActiveCollab\Shade\Ability\BuildableInterface;

interface LinkerInterface
{
    public function getUrl(string $path, BuildableInterface $relativeTo, string $locale = null): string;
    public function pageLevelToPrefix(int $page_level, string $locale = null);
}
