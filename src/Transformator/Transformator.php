<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator;

use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\Element\ElementInterface;
use ActiveCollab\Shade\ProjectInterface;
use ActiveCollab\Shade\ThemeInterface;

abstract class Transformator implements TransformatorInterface
{
    private $project;
    private $theme;

    public function __construct(ProjectInterface $project, ThemeInterface $theme)
    {
        $this->project = $project;
        $this->theme = $theme;
    }

    protected function getProject(): ProjectInterface
    {
        return $this->project;
    }

    protected function getTheme(): ThemeInterface
    {
        return $this->theme;
    }

    public function transform(ElementInterface $current_element, string $markdown_content): string
    {
        $html = Shade::markdownToHtml($markdown_content);

        return $html;
    }
}
