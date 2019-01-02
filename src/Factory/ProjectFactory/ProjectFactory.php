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
use ActiveCollab\Shade\Transformator\TransformatorInterface;

class ProjectFactory implements ProjectFactoryInterface
{
    private $loader;
    private $renderer;
    private $transformator;

    public function __construct(
        LoaderInterface $loader,
        RendererInterface $renderer,
        TransformatorInterface $transformator
    )
    {
        $this->loader = $loader;
        $this->renderer = $renderer;
        $this->transformator = $transformator;
    }

    public function createProject(string $project_path): ProjectInterface
    {
        return new Project($project_path, $this->loader, $this->renderer, $this->transformator);
    }
}
