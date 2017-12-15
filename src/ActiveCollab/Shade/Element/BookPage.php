<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade;

/**
 * Framework level help book page class.
 *
 * @package Shade
 */
class BookPage extends Element
{
    /**
     * Parent book name.
     *
     * @var string
     */
    private $book_name;

    /**
     * Construct and load help element.
     *
     * @param string      $module
     * @param Book|string $book
     * @param string      $path
     * @param bool        $load
     */
    public function __construct($module, $book, $path, $load = true)
    {
        $this->book_name = $book instanceof Book ? $book->getShortName() : $book;

        parent::__construct($module, $path, $load);
    }

    /**
     * Return book name.
     *
     * @return string
     */
    public function getBookName()
    {
        return $this->book_name;
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
            $slug = $this->getProperty('slug');

            if (empty($slug)) {
                $this->slug = Shade::slug($this->getTitle());
            } else {
                $this->slug = $slug;
            }
        }

        return $this->slug;
    }

    /**
     * Return page level in the build structure.
     *
     * @return int
     */
    public function getPageLevel()
    {
        return 2;
    }
}
