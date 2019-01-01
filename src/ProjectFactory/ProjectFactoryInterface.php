<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\ProjectFactory;

use ActiveCollab\Shade\ProjectInterface;

interface ProjectFactoryInterface
{
    public function createProject(string $project_path): ProjectInterface;
}
