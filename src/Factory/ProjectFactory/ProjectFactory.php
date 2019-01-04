<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Factory\ProjectFactory;

use ActiveCollab\Shade\Linker\LinkerInterface;
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
    private $linker;

    public function __construct(
        LoaderInterface $loader,
        RendererInterface $renderer,
        TransformatorInterface $transformator,
        LinkerInterface $linker
    )
    {
        $this->loader = $loader;
        $this->renderer = $renderer;
        $this->transformator = $transformator;
        $this->linker = $linker;
    }

    public function createProject(string $project_path): ProjectInterface
    {
        return new Project(
            $project_path,
            $this->loader,
            $this->renderer,
            $this->transformator,
            $this->linker
        );
    }
}
