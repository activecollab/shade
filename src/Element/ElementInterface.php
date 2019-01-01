<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\BuildableInterface;
use ActiveCollab\Shade\Project;

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
     * @return Project
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
