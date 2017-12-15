<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade;

/**
 * What's new article element.
 *
 * @package Shade
 */
class WhatsNewArticle extends Element
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
     * @param string $module
     * @param string $version_number
     * @param string $path
     * @param bool   $load
     */
    public function __construct($module, $version_number, $path, $load = true)
    {
        $this->version_number = $version_number;

        parent::__construct($module, $path, $load);
    }

    /**
     * Return book's short name.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->getSlug();
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
