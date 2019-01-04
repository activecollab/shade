<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Loader\Result;

interface LoaderResultInterface
{
    public function getIndexFilePath(): string;
    public function getProperties(): array;
    public function getProperty(string $name, string $default = null): ?string;
    public function getBody(): string;
}
