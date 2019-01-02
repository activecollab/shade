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
     * @param ProjectInterface  $project
     * @param LoaderInterface   $loader
     * @param RendererInterface $renderer
     * @param Book|string       $book
     * @param string            $path
     * @param bool              $load
     */
    public function __construct(ProjectInterface $project, LoaderInterface $loader, RendererInterface $renderer, $book, $path, $load = true)
    {
        $this->book_name = $book instanceof Book ? $book->getShortName() : $book;

        parent::__construct($project, $loader, $renderer, $path, $load);
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

    public function getShortName(): string
    {
        return $this->getSlug();
    }

    public function getPageLevel(): int
    {
        return 2;
    }
}
