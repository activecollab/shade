<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Factory\ProjectFactory;

use ActiveCollab\Shade\Loader\LoaderInterface;
use ActiveCollab\Shade\Project\Project;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Renderer\RendererInterface;

class ProjectFactory implements ProjectFactoryInterface
{
    private $loader;
    private $renderer;

    public function __construct(LoaderInterface $loader, RendererInterface $renderer)
    {
        $this->loader = $loader;
        $this->renderer = $renderer;
    }

    public function createProject(string $project_path): ProjectInterface
    {
        return new Project($project_path, $this->loader, $this->renderer);
    }
}
