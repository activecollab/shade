<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\ElementFileParser;
use ActiveCollab\Shade\Project;
use ActiveCollab\Shade\ProjectInterface;

abstract class Element implements ElementInterface
{
    use ElementFileParser;

    private $project;

    public function __construct(ProjectInterface $project, string $path, bool $load = true)
    {
        $this->project = $project;
        $this->path = $path;

        if ($load) {
            $this->load();
        }
    }

    /**
     * @var string
     */
    protected $path;

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get folder name.
     *
     * @return string
     */
    public function getFolderName()
    {
        return basename($this->path);
    }

    public function &getProject()
    {
        return $this->project;
    }

    /**
     * Book's short name.
     *
     * @var string
     */
    protected $short_name;

    /**
     * Return book's short name.
     *
     * @return string
     */
    public function getShortName()
    {
        if ($this->short_name === null) {
            $this->short_name = str_replace('_', '-', basename($this->path));
        }

        return $this->short_name;
    }

    /**
     * Return element title.
     *
     * @return string
     */
    abstract public function getTitle();

    public function getPageLevel(): int
    {
        return 1;
    }
}
