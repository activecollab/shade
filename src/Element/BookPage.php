<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Linker\LinkerInterface;
use ActiveCollab\Shade\Loader\LoaderInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Renderer\RendererInterface;

class BookPage extends Element
{
    private $book_name;

    public function __construct(
        ProjectInterface $project,
        LoaderInterface $loader,
        RendererInterface $renderer,
        LinkerInterface $linker,
        string $book_name,
        string $path,
        bool $load = true
    )
    {
        parent::__construct($project, $loader, $renderer, $linker, $path, $load);

        $this->book_name = $book_name;
    }

    public function getBookName(): string
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

    public function getUrl(BuildableInterface $relativeTo, string $locale = null): string
    {
        return $this->getLinker()->getUrl(
            'books/' . $this->getBookName() . '/' . $this->getSlug(). '.html',
            $relativeTo,
            $locale
        );
    }
}
