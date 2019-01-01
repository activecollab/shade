<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Element\Finder;

use ActiveCollab\Shade\Element\Book;
use ActiveCollab\Shade\Element\BookPage;
use ActiveCollab\Shade\Element\Release;
use ActiveCollab\Shade\Element\Video;
use ActiveCollab\Shade\Element\WhatsNewArticle;
use ActiveCollab\Shade\NamedList;
use Exception;

interface ElementFinderInterface
{
    /**
     * Set a custom finder.
     *
     * @param  string    $name
     * @param  callable  $callback
     * @throws Exception
     */
    public function setCustomFinder($name, $callback);

    /**
     * Get path of books folder.
     *
     * @param  string $locale
     * @return string
     */
    function getBooksPath($locale);

    /**
     * @param  string $locale
     * @return Book[]
     */
    function getBooks($locale = null);

    /**
     * Get book by short name.
     *
     * @param  string    $name
     * @param  string    $locale
     * @return Book|null
     */
    function getBook($name, $locale = null);

    /**
     * @param  Book                      $book
     * @return NamedList|BookPage[]|null
     */
    function getBookPages(Book $book);

    /**
     * Return path to the folder where we expect to find videos.
     *
     * @param  string $locale
     * @return string
     */
    function getVideosPath($locale);

    /**
     * @param  string            $locale
     * @return Video[]|NamedList
     */
    function getVideos($locale = null);

    /**
     * Return a video.
     *
     * @param  string      $name
     * @param  string|null $locale
     * @return Video|null
     */
    function getVideo($name, $locale = null);

    /**
     * Return path to the folder where we expect to find what's new articles.
     *
     * @param  string $locale
     * @return string
     */
    function getWhatsNewArticlesPath($locale);

    /**
     * @param  string|null                 $locale
     * @return WhatsNewArticle[]|NamedList
     */
    function getWhatsNewArticles($locale = null);

    /**
     * @param  string                 $name
     * @param  string|null            $locale
     * @return WhatsNewArticle[]|null
     */
    function getWhatsNewArticle($name, $locale = null);

    /**
     * Return path to the folder where we expect to find release entries.
     *
     * @param  string $locale
     * @return string
     */
    function getReleasesPath($locale);

    /**
     * @param  string|null $locale
     * @return Release[]
     */
    function getReleases($locale = null);
}
