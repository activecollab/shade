<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade;

use Exception;

class Theme implements ThemeInterface
{
    private $path;

    function __construct(string $path)
    {
        if (is_dir($path)) {
            $this->path = $path;
        } else {
            throw new Exception("Path '$path' is not a valid Shade theme");
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
