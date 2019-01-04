<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\MarkdownToHtml;

use ActiveCollab\Shade\Shade;

class MarkdownToHtml implements MarkdownToHtmlInterface
{
    public function markdownToHtml(string $markdown): string
    {
        return Shade::markdownToHtml($markdown);
    }
}
