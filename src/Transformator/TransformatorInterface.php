<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator;

use ActiveCollab\Shade\Element\ElementInterface;

interface TransformatorInterface
{
    public function transform(ElementInterface $current_element, string $markdown_content): string;
}
