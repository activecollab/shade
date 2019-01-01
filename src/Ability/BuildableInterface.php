<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Ability;

interface BuildableInterface
{
    public function getPageLevel(): int;
    public function getIndexFilePath(): string;
}
