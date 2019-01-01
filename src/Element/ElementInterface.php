<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Ability\LoadableInterface;
use ActiveCollab\Shade\Ability\RenderableInterface;
use ActiveCollab\Shade\Project\Project;

interface ElementInterface extends BuildableInterface, LoadableInterface, RenderableInterface
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

    /**
     * Return book's short name.
     *
     * @return string
     */
    public function getShortName();

    /**
     * Return element title.
     *
     * @return string
     */
    public function getTitle();
}
