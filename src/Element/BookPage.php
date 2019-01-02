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

class BookPage extends Element
{
    private $book_name;

    public function __construct(
        ProjectInterface $project,
        LoaderInterface $loader,
        RendererInterface $renderer,
        TransformatorInterface $transformator,
        string $book_name,
        string $path,
        bool $load = true
    )
    {
        parent::__construct($project, $loader, $renderer, $transformator, $path, $load);

        $this->book_name = $book_name;
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
