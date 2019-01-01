<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Project;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Ability\LoadableInterface;
use ActiveCollab\Shade\Ability\RenderableInterface;
use ActiveCollab\Shade\Element\Book;
use ActiveCollab\Shade\Element\BookPage;
use ActiveCollab\Shade\Element\Finder\ElementFinder;
use ActiveCollab\Shade\Element\Release;
use ActiveCollab\Shade\Element\Video;
use ActiveCollab\Shade\Element\WhatsNewArticle;
use ActiveCollab\Shade\Error\ThemeNotFoundError;
use ActiveCollab\Shade\NamedList;
use ActiveCollab\Shade\Theme;
use ActiveCollab\Shade\VideoPlayer\VideoPlayer;

interface ProjectInterface extends BuildableInterface, LoadableInterface, RenderableInterface
{
    /**
     * Return project name.
     *
     * @return string
     */
    function getName();

    /**
     * Return true if this project is multilingual.
     *
     * @return bool
     */
    function isMultilingual();

    /**
     * Return a list of project locales.
     *
     * @return array
     */
    function getLocales();

    /**
     * @return string
     */
    function getLocale();

    /**
     * Return short locale code.
     *
     * @return string
     */
    function getShortLocale();

    /**
     * @param string $value
     */
    function setLocale($value);

    /**
     * Return default build target.
     *
     * @return string|null
     */
    function getDefaultBuildTarget();

    /**
     * Return default locale, for multilingual projects.
     *
     * @return string|null
     */
    function getDefaultLocale();

    /**
     * Return name of the  default locale.
     *
     * @return string|null
     */
    function getDefaultLocaleName();

    /**
     * Return build theme.
     *
     * @param  string|null        $name
     * @return Theme
     * @throws ThemeNotFoundError
     */
    function getBuildTheme($name = null);

    /**
     * Return name of the default build theme.
     *
     * @return string
     */
    function getDefaultBuildTheme();

    /**
     * @return array
     */
    function getSocialLinks();

    /**
     * Return configuration option.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getConfigurationOption($name, $default = null);

    /**
     * Return project path.
     *
     * @return string
     */
    function getPath();

    /**
     * Return all project stories.
     *
     * @param  string|null      $locale
     * @return Book[]|NamedList
     */
    function getBooks($locale = null);

    /**
     * Get book by short name.
     *
     * @param  string      $name
     * @param  string|null $locale
     * @return Book|null
     */
    function getBook($name, $locale = null);

    /**
     * @param  Book                 $book
     * @return BookPage[]|NamedList
     */
    function getBookPages(Book $book);

    /**
     * Return array of common questions.
     *
     * @param  string|null $locale
     * @return array
     */
    public function getCommonQuestions($locale = null);

    /**
     * @param  string|null                 $locale
     * @return WhatsNewArticle[]|NamedList
     */
    function getWhatsNewArticles($locale = null);

    /**
     * Return what's new article.
     *
     * @param  string               $name
     * @param  string|null          $locale
     * @return WhatsNewArticle|null
     */
    function getWhatsNewArticle($name, $locale = null);

    /**
     * Return releases.
     *
     * @param  string|null $locale
     * @return Release[]
     */
    function getReleases($locale = null);

    /**
     * Return array of video groups.
     *
     * @return array
     */
    public function getVideoGroups();

    /**
     * Return project videos.
     *
     * @param  string|null       $locale
     * @return Video[]|NamedList
     */
    function getVideos($locale = null);

    /**
     * @param  string      $name
     * @param  string|null $locale
     * @return Video|null
     */
    function getVideo($name, $locale = null);

    /**
     * Return true if this is a valid project.
     *
     * @return bool
     */
    function isValid();

    /**
     * @return ElementFinder
     */
    function getFinder();

    /**
     * Return instance that will be used to render videos.
     *
     * @return VideoPlayer
     */
    public function getVideoPlayer();

    /**
     * Return temp path.
     *
     * @return string
     */
    public function getTempPath();

    /**
     * @return Project
     */
    public function getProject();
}
