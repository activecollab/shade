<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\Ability\BuildableInterface;

interface ElementInterface extends BuildableInterface
{
    /**
     * @return string
     */
    public function getPath();

    /**
     * Get folder name.
     *
     * @return string
     */
    public function getFolderName();

    /**
     * @return \ActiveCollab\Shade\Project\Project
     */
    public function getProject();
    public function getShortName(): string;
    public function getSlug(): string;
    public function getTitle(): string;
}
