<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\Loader\LoaderInterface;
use ActiveCollab\Shade\Loader\Result\LoaderResultInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Renderer\RendererInterface;
use ActiveCollab\Shade\Transformator\Transformator;
use ActiveCollab\Shade\Transformator\TransformatorInterface;

abstract class Element implements ElementInterface
{
    private $project;
    private $loader;
    private $renderer;

    /**
     * @var LoaderResultInterface|null
     */
    private $loadResult;

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
            $this->loadResult = $this->loader->load($this);
        }
    }

    public function renderBody(): string
    {
        return $this->renderer->renderElementBody($this);
    }

    /**
     * @var string
     */
    private $index_file_path;

    /**
     * Get index file path.
     *
     * @return string
     */
    public function getIndexFilePath(): string
    {
        if (empty($this->index_file_path)) {
            $this->index_file_path = is_dir($this->getPath()) ? $this->getPath() . '/index.md' : $this->getPath();
        }

        return $this->index_file_path;
    }

    public function isLoaded(): bool
    {
        return !empty($this->loadResult);
    }

    public function getProperty(string $name, string $default = null): ?string
    {
        return $this->loadResult instanceof LoaderResultInterface
            ? $this->loadResult->getProperty($name, $default)
            : $default;
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

    protected $short_name;

    public function getShortName(): string
    {
        if ($this->short_name === null) {
            $this->short_name = str_replace('_', '-', $this->getFolderName());
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
