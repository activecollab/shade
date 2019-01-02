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
use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\Transformator\Transformator;
use ActiveCollab\Shade\Transformator\TransformatorInterface;

abstract class Element implements ElementInterface
{
    private $project;
    private $loader;
    private $renderer;
    private $path;

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

    public function getPath(): string
    {
        return $this->path;
    }

    protected function getFolderName(): string
    {
        return basename($this->path);
    }

    public function getProject(): ProjectInterface
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

    private $slug;

    public function getSlug(): string
    {
        if ($this->slug === null) {
            $slug = $this->getProperty('slug');

            if (empty($slug)) {
                $this->slug = Shade::slug($this->getTitle());
            } else {
                $this->slug = $slug;
            }
        }

        return $this->slug;
    }

    private $title;

    public function getTitle(): string
    {
        if ($this->title === null) {
            $title = $this->getProperty('title');

            if (empty($title)) {
                $basename = $this->getFolderName();

                $first_dot = strpos($basename, '.');
                $second_dot = strpos($basename, '.', $first_dot + 1);

                $this->title = trim(substr($basename, $first_dot + 1, $second_dot - $first_dot - 1));
            } else {
                $this->title = $title;
            }
        }

        return $this->title;
    }

    public function getPageLevel(): int
    {
        return 1;
    }
}
