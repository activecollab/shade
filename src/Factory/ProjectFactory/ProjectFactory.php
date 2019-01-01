<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Factory\ProjectFactory;

use ActiveCollab\Shade\Project;
use ActiveCollab\Shade\ProjectInterface;
use ActiveCollab\Shade\Renderer\RendererInterface;

class ProjectFactory implements ProjectFactoryInterface
{
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function createProject(string $project_path): ProjectInterface
    {
        return new Project($project_path, $this->renderer);
    }
}
