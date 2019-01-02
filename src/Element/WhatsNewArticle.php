<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\Linker\LinkerInterface;
use ActiveCollab\Shade\Loader\LoaderInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Renderer\RendererInterface;

class WhatsNewArticle extends Element
{
    private $version_number;

    public function __construct(
        ProjectInterface $project,
        LoaderInterface $loader,
        RendererInterface $renderer,
        LinkerInterface $linker,
        string $version_number,
        string $path,
        bool $load = true
    )
    {
        parent::__construct($project, $loader, $renderer, $linker, $path, $load);

        $this->version_number = $version_number;
    }

    public function getShortName(): string
    {
        return $this->getSlug();
    }

    public function getVersionNumber(): string
    {
        return $this->version_number;
    }
}
