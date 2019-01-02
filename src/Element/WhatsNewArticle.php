<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\Loader\LoaderInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Renderer\RendererInterface;
use ActiveCollab\Shade\Shade;

class WhatsNewArticle extends Element
{
    private $version_number;

    public function __construct(
        ProjectInterface $project,
        LoaderInterface $loader,
        RendererInterface $renderer,
        string $version_number,
        string $path,
        bool $load = true
    )
    {
        parent::__construct($project, $loader, $renderer, $path, $load);

        $this->version_number = $version_number;
    }

    public function getShortName(): string
    {
        return $this->getSlug();
    }

    public function getVersionNumber(): string
    {
        return $this->version_number;
    }

    /**
     * Cached title.
     *
     * @var string
     */
    protected $title;

    /**
     * Return page title.
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->title === null) {
            $title = $this->getProperty('title');

            if (empty($title)) {
                $basename = basename($this->path);

                $first_dot = strpos($basename, '.');
                $second_dot = strpos($basename, '.', $first_dot + 1);

                $this->title = trim(substr($basename, $first_dot + 1, $second_dot - $first_dot - 1));
            } else {
                $this->title = $title;
            }
        }

        return $this->title;
    }

    /**
     * Cached slug value.
     *
     * @var string
     */
    protected $slug;

    /**
     * Return page slug.
     *
     * @return string
     */
    public function getSlug()
    {
        if ($this->slug === null) {
            $this->slug = ''; // str_replace('.', '-', $this->version_number) . '-';

            $slug = $this->getProperty('slug');

            if (empty($slug)) {
                $this->slug .= Shade::slug($this->getTitle());
            } else {
                $this->slug .= $slug;
            }
        }

        return $this->slug;
    }

}
