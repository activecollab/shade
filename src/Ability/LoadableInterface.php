<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Ability;

interface LoadableInterface
{
    const PROPERTIES_SEPARATOR = '================================================================';

    public function getIndexFilePath(): string;
    public function getProperty(string $name, string $default = null): ?string;
    public function isLoaded(): bool;
}
