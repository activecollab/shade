<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator;

use ActiveCollab\Shade\Shade;

class Transformator implements TransformatorInterface
{
    public function transform(string $markdown_content): string
    {
        $html = Shade::markdownToHtml($markdown_content);

        return $html;
    }
}
