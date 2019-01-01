<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\ElementFileParser;
use ActiveCollab\Shade\Loader\LoaderInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Renderer\RendererInterface;
use ActiveCollab\Shade\Transformator\Transformator;
use ActiveCollab\Shade\Transformator\TransformatorInterface;

abstract class Element implements ElementInterface
{
    use ElementFileParser;

    private $project;
    private $loader;
    private $renderer;

    public function __construct(
        ProjectInterface $project,
        LoaderInterface $loader,
        RendererInterface $renderer,
        string $path,
        bool $load = true
    )
    {
        $this->project = $project;
        $this->loader = $loader;
        $this->renderer = $renderer;
        $this->path = $path;

        if ($load) {
            $this->load();
        }
    }

    public function renderBody(): string
    {
        return $this->renderer->renderElementBody($this);
    }

    public function getTransformator(): TransformatorInterface
    {
        return new Transformator();
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
