<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\ProjectInterface;
use ActiveCollab\Shade\Renderer\RendererInterface;
use DateTime;

/**
 * Release element (for release notes).
 *
 * @package ActiveCollab\Shade\Shade\Element
 */
class Release extends Element
{
    /**
     * Application version number.
     *
     * @var string
     */
    private $version_number;

    /**
     * Construct and load help element.
     *
     * @param ProjectInterface  $project
     * @param RendererInterface $renderer
     * @param string            $version_number
     * @param string            $path
     * @param bool              $load
     */
    public function __construct(ProjectInterface $project, RendererInterface $renderer, $version_number, $path, $load = true)
    {
        $this->version_number = $version_number;

        parent::__construct($project, $renderer, $path, $load);
    }

    /**
     * @return string
     */
    function getTitle()
    {
        return $this->getVersionNumber();
    }

    /**
     * Return in which version change was introduced.
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->version_number;
    }

    /**
     * Return page slug.
     *
     * @return string
     */
    public function getSlug()
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
