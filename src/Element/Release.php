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
use ActiveCollab\Shade\Transformator\TransformatorInterface;
use DateTime;

class Release extends Element
{
    private $version_number;

    public function __construct(
        ProjectInterface $project,
        LoaderInterface $loader,
        RendererInterface $renderer,
        TransformatorInterface $transformator,
        string $version_number,
        string $path,
        bool $load = true
    )
    {
        $this->version_number = $version_number;

        parent::__construct($project, $loader, $renderer, $transformator, $path, $load);
    }

    public function getTitle(): string
    {
        return $this->getVersionNumber();
    }

    public function getVersionNumber(): string
    {
        return $this->version_number;
    }

    public function getSlug(): string
    {
        return str_replace('.', '-', $this->version_number);
    }

    /**
     * @var DateTime|null
     */
    private $release_date = false;

    /**
     * @return DateTime|null
     */
    public function getReleaseDate()
    {
        if ($this->release_date === false) {
            if ($this->getProperty('release_date')) {
                $this->release_date = new DateTime($this->getProperty('release_date'));
            } else {
                $this->release_date = null;
            }
        }

        return $this->release_date;
    }
}
