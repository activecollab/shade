<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade;

use ActiveCollab\Shade\Element\Book;
use ActiveCollab\Shade\Element\BookPage;
use ActiveCollab\Shade\Element\Release;
use ActiveCollab\Shade\Element\Video;
use ActiveCollab\Shade\Element\WhatsNewArticle;
use ActiveCollab\Shade\Error\ParseJsonError;
use ActiveCollab\Shade\Error\ThemeNotFoundError;
use ActiveCollab\Shade\Transformator\TransformatorInterface;
use ActiveCollab\Shade\VideoPlayer\VideoPlayer;

class Project implements ProjectInterface
{
    use ElementFileParser;

    private $path;
    private $configuration = [];

    public function __construct(string $path)
    {
        $this->path = $path;

        if ($this->isValid()) {
            $configuration_json = file_get_contents($this->path . '/project.json');
            $this->configuration = json_decode($configuration_json, true);

            if ($this->configuration === null) {
                throw new ParseJsonError($configuration_json, json_last_error());
            }

            if (empty($this->configuration)) {
                $this->configuration = [];
            }

            $this->load();
        }
    }

    public function getTransformator(): TransformatorInterface
    {
        return null;
    }

    /**
     * Return project name.
     *
     * @return string
     */
    function getName()
    {
        return $this->getConfigurationOption('name', basename($this->path));
    }

    /**
     * Return true if this project is multilingual.
     *
     * @return bool
     */
    function isMultilingual()
    {
        return $this->getConfigurationOption('is_multilingual') || count($this->getLocales()) > 1;
    }

    /**
     * @var array
     */
    private $locales = false;

    /**
     * Return a list of project locales.
     *
     * @return array
     */
    function getLocales()
    {
        if ($this->locales === false) {
            $this->locales = [$this->getDefaultLocale() => $this->getDefaultLocaleName()];

            if (is_array($this->getConfigurationOption('locales'))) {
                foreach ($this->getConfigurationOption('locales') as $code => $name) {
                    $this->locales[$code] = $name;
                }
            }
        }

        return $this->locales;
    }

    /**
     * @var string
     */
    private $locale = false;

    /**
     * @return string
     */
    function getLocale()
    {
        if (empty($this->locale)) {
            $this->locale = $this->getDefaultLocale();

            if (empty($this->locale)) {
                $this->locale = 'en';
            }
        }

        return $this->locale;
    }

    /**
     * Return short locale code.
     *
     * @return string
     */
    function getShortLocale()
    {
        $bits = explode('_', $this->getLocale());

        return array_shift($bits);
    }

    /**
     * @param string $value
     */
    function setLocale($value)
    {
        $this->locale = $value;
    }

    /**
     * @var string
     */
    private $default_build_target;

    /**
     * Return default build target.
     *
     * @return string|null
     */
    function getDefaultBuildTarget()
    {
        if (empty($this->default_build_target)) {
            $this->default_build_target = $this->getConfigurationOption('default_build_target');

            if (empty($this->default_build_target)) {
                $this->default_build_target = $this->path . '/build';
            }
        }

        return $this->default_build_target;
    }

    /**
     * Return default locale, for multilingual projects.
     *
     * @return string|null
     */
    function getDefaultLocale()
    {
        return $this->getConfigurationOption('default_locale');
    }

    /**
     * Return name of the  default locale.
     *
     * @return string|null
     */
    function getDefaultLocaleName()
    {
        $default_locale_name = $this->getConfigurationOption('default_locale_name');

        if (empty($default_locale_name)) {
            $default_locale_name = $this->getDefaultLocale();
        }

        return $default_locale_name;
    }

    /**
     * Return build theme.
     *
     * @param  string|null        $name
     * @return Theme
     * @throws ThemeNotFoundError
     */
    function getBuildTheme($name = null)
    {
        if ($name) {
            $theme_path = __DIR__ . "/Themes/$name"; // Input
        } elseif (is_dir($this->getPath() . '/theme')) {
            $theme_path = $this->getPath() . '/theme'; // Project specific theme
        } else {
            $theme_path = __DIR__ . '/Themes/' . $this->getDefaultBuildTheme(); // Default built in theme
        }

        if ($theme_path && is_dir($theme_path)) {
            return new Theme($theme_path);
        } else {
            throw new ThemeNotFoundError($name, $theme_path);
        }
    }

    /**
     * Return name of the default build theme.
     *
     * @return string
     */
    function getDefaultBuildTheme()
    {
        return $this->getConfigurationOption('default_build_theme', 'bootstrap');
    }

    /**
     * @var array
     */
    private $social_links = false;

    /**
     * @return array
     */
    function getSocialLinks()
    {
        if ($this->social_links === false) {
            $this->social_links = [];

            if (is_array($this->getConfigurationOption('social_links'))) {
                foreach ($this->getConfigurationOption('social_links') as $service => $handle) {
                    switch ($service) {
                        case 'twitter':
                            $this->social_links[$service] = ['name' => 'Twitter', 'url' => "https://twitter.com/{$handle}", 'icon' => "images/icon_{$service}.png"];
                            break;
                        case 'facebook':
                            $this->social_links[$service] = ['name' => 'Facebook', 'url' => "https://www.facebook.com/{$handle}", 'icon' => "images/icon_{$service}.png"];
                            break;
                        case 'google':
                            $this->social_links[$service] = ['name' => 'Google+', 'url' => "https://plus.google.com/+{$handle}", 'icon' => "images/icon_{$service}.png"];
                            break;
                    }
                }
            }
        }

        return $this->social_links;
    }

    /**
     * Return configuration option.
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getConfigurationOption($name, $default = null)
    {
        return isset($this->configuration[$name]) && $this->configuration[$name] ? $this->configuration[$name] : $default;
    }

    /**
     * Return project path.
     *
     * @return string
     */
    function getPath()
    {
        return $this->path;
    }

    // ---------------------------------------------------
    //  Books
    // ---------------------------------------------------

    /**
     * Return all project stories.
     *
     * @param  string|null      $locale
     * @return Book[]|NamedList
     */
    function getBooks($locale = null)
    {
        return $this->getFinder()->getBooks($locale);
    }

    /**
     * Get book by short name.
     *
     * @param  string      $name
     * @param  string|null $locale
     * @return Book|null
     */
    function getBook($name, $locale = null)
    {
        return $this->getFinder()->getBook($name, $locale);
    }

    /**
     * @param  Book                 $book
     * @return BookPage[]|NamedList
     */
    function getBookPages(Book $book)
    {
        return $this->getFinder()->getBookPages($book);
    }

    /**
     * Return array of common questions.
     *
     * @param  string|null $locale
     * @return array
     */
    public function getCommonQuestions($locale = null)
    {
        $result = [];

        foreach ($this->getBooks($locale) as $book) {
            $book->populateCommonQuestionsList($result);
        }

        usort($result, function ($a, $b) {
            if ($a['position'] == $b['position']) {
                return 0;
            }

            return ($a['position'] < $b['position']) ? -1 : 1;
        });

        return $result;
    }

    // ---------------------------------------------------
    //  What's New Articles
    // ---------------------------------------------------

    /**
     * @param  string|null                 $locale
     * @return WhatsNewArticle[]|NamedList
     */
    function getWhatsNewArticles($locale = null)
    {
        return $this->getFinder()->getWhatsNewArticles($locale);
    }

    /**
     * Return what's new article.
     *
     * @param  string               $name
     * @param  string|null          $locale
     * @return WhatsNewArticle|null
     */
    function getWhatsNewArticle($name, $locale = null)
    {
        return $this->getFinder()->getWhatsNewArticle($name, $locale);
    }

    // ---------------------------------------------------
    //  Releases
    // ---------------------------------------------------


    /**
     * Return releases.
     *
     * @param  string|null $locale
     * @return Release[]
     */
    function getReleases($locale = null)
    {
        return $this->getFinder()->getReleases($locale);
    }

    // ---------------------------------------------------
    //  Video
    // ---------------------------------------------------

    /**
     * @var array
     */
    private $video_groups = false;

    /**
     * Return array of video groups.
     *
     * @return array
     */
    public function getVideoGroups()
    {
        if ($this->video_groups === false) {
            $this->video_groups = $this->getConfigurationOption('video_groups');

            if (!is_array($this->video_groups)) {
                $this->video_groups = [
                    Video::GETTING_STARTED => Shade::lang('Getting Started'),
                ];
            }
        }

        return $this->video_groups;
    }

    /**
     * Return project videos.
     *
     * @param  string|null       $locale
     * @return Video[]|NamedList
     */
    function getVideos($locale = null)
    {
        return $this->getFinder()->getVideos($locale);
    }

    /**
     * @param  string      $name
     * @param  string|null $locale
     * @return Video|null
     */
    function getVideo($name, $locale = null)
    {
        return $this->getFinder()->getVideo($name, $locale);
    }

    /**
     * Return true if this is a valid project.
     *
     * @return bool
     */
    function isValid()
    {
        return is_dir($this->path) && is_file($this->path . '/project.json');
    }

    /**
     * @var ElementFinder
     */
    private $finder;

    /**
     * @return ElementFinder
     */
    function &getFinder()
    {
        if (empty($this->finder)) {
            $this->finder = new ElementFinder($this);

            if (is_file($this->getPath() . '/finders.php')) {
                $finders = require $this->getPath() . '/finders.php';

                if (is_array($finders)) {
                    foreach ($finders as $finder_name => $callback) {
                        $this->finder->setCustomFinder($finder_name, $callback);
                    }
                }
            }
        }

        return $this->finder;
    }

    /**
     * @var VideoPlayer
     */
    private $video_player;

    /**
     * Return instance that will be used to render videos.
     *
     * @return VideoPlayer
     */
    public function getVideoPlayer()
    {
        if (empty($this->video_player)) {
            $this->video_player = new Shade\VideoPlayer\WistiaVideoPlayer($this);
        }

        return $this->video_player;
    }

    /**
     * Return temp path.
     *
     * @return string
     */
    public function getTempPath()
    {
        return $this->path . '/temp';
    }

    /**
     * @return Project
     */
    public function &getProject()
    {
        return $this;
    }

    public function getPageLevel(): int
    {
        return 0;
    }
}
