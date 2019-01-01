<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element\Finder;

use ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Release, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\WhatsNewArticle, ActiveCollab\Shade\Renderer\RendererInterface;
use ActiveCollab\Shade\NamedList;
use ActiveCollab\Shade\Project;
use ActiveCollab\Shade\ProjectInterface;
use ActiveCollab\Shade\Shade;
use DirectoryIterator, Exception;

class ElementFinder implements ElementFinderInterface
{
    private $project;
    private $renderer;

    /**
     * @var callable[]
     */
    protected $finders = [];

    function __construct(ProjectInterface $project, RendererInterface $renderer)
    {
        $this->project = $project;
        $this->renderer = $renderer;

        $this->finders = [

            /**
             * Find book files.
             *
             * @param string $books_path
             */
            'findBookDirs' => function ($books_path) {
                $dirs = [];

                if (is_dir($books_path)) {
                    foreach (new DirectoryIterator($books_path) as $dir) {
                        if (!$dir->isDot() && $dir->isDir()) {
                            $dirs[] = $dir->getPathname();
                        }
                    }
                }

                return $dirs;
            },

            /**
             * Return array of pages that are in a given book.
             *
             * @param string $pages_path
             */
            'findBookPageFiles' => function ($pages_path) {
                $files = [];

                if (is_dir($pages_path)) {
                    foreach (new DirectoryIterator($pages_path) as $file) {
                        if ($file->isFile() && $file->getExtension() == 'md') {
                            $files[] = $file->getPathname();
                        }
                    }

                    sort($files);
                }

                return $files;
            },

            /**
             * Return array of video files.
             *
             * @param string $videos_path
             */
            'findVideoFiles' => function ($videos_path) {
                $files = [];

                if (is_dir($videos_path)) {
                    foreach (new DirectoryIterator($videos_path) as $file) {
                        if ($file->isFile() && $file->getExtension() == 'md') {
                            $files[] = $file->getPathname();
                        }
                    }

                    sort($files);
                }

                return $files;
            },

            /**
             * Return releases.
             *
             * @param string $releases_path
             */
            'findReleaseFiles' => function ($releases_path) {
                $files = [];

                if (is_dir($releases_path)) {
                    foreach (new DirectoryIterator($releases_path) as $file) {
                        if ($file->isFile() && $file->getExtension() == 'md') {
                            $version_number = substr($file->getFilename(), 0, strlen($file->getFilename()) - 3);

                            if (Shade::isValidVersionNumber($version_number)) {
                                $files[$version_number] = $file->getPathname();
                            }
                        }
                    }

                    uksort($files, function ($a, $b) {
                        return version_compare($b, $a);
                    });
                }

                return $files;
            },

            /**
             * Return what's new files.
             *
             * @param array $whats_new_articles_path
             */
            'findWhatsNewFiles' => function ($whats_new_articles_path) {
                $files = [];

                if (is_dir($whats_new_articles_path)) {
                    foreach (new DirectoryIterator($whats_new_articles_path) as $version_dir) {
                        if (!$version_dir->isDot() && $version_dir->isDir() && Shade::isValidVersionNumber($version_dir->getFilename())) {
                            $version_num = $version_dir->getFilename();

                            foreach (new DirectoryIterator($version_dir->getPathname()) as $file) {
                                if ($file->isFile() && $file->getExtension() == 'md') {
                                    $files[$version_num][] = $file->getPathname();
                                }
                            }
                        }
                    }

                    if (count($files)) {
                        ksort($files);

                        foreach ($files as $version_num => $version_articles) {
                            sort($files[$version_num]);
                        }
                    }
                }

                return $files;
            },
        ];
    }

    /**
     * Set a custom finder.
     *
     * @param  string    $name
     * @param  callable  $callback
     * @throws Exception
     */
    public function setCustomFinder($name, $callback)
    {
        if (empty($this->finders[$name])) {
            throw new Exception("Unknown finder '$name'");
        }

        if (!is_callable($callback)) {
            throw new Exception('Callback needs to be callable');
        }

        $this->finders[$name] = $callback;
    }

    /**
     * Get path of books folder.
     *
     * @param  string $locale
     * @return string
     */
    function getBooksPath($locale)
    {
        return $this->getLocalizedPath('books', $locale);
    }

    /**
     * @param  string $locale
     * @return Book[]
     */
    function getBooks($locale = null)
    {
        if (empty($locale)) {
            $locale = $this->project->getDefaultLocale();
        }

        $dirs = call_user_func($this->finders['findBookDirs'], $this->getBooksPath($locale), $locale);

        $result = [];

        foreach ($dirs as $dir) {
            $book = new Book($this->project, $this->renderer, $dir);

            if ($book->isLoaded()) {
                $result[$book->getShortName()] = $book;
            }
        }

        if (count($result)) {
            uasort($result, function (Book $a, Book $b) {
                if ($a->getPosition() == $b->getPosition()) {
                    return 0;
                } else {
                    return $a->getPosition() > $b->getPosition() ? 1 : -1;
                }
            });
        }

        return $result;
    }

    /**
     * Get book by short name.
     *
     * @param  string    $name
     * @param  string    $locale
     * @return Book|null
     */
    function getBook($name, $locale = null)
    {
        foreach ($this->getBooks($locale) as $book) {
            if ($book->getShortName() === $name) {
                return $book;
            }
        }

        return null;
    }

    /**
     * @param  Book                      $book
     * @return NamedList|BookPage[]|null
     */
    function getBookPages(Book $book)
    {
        $files = call_user_func($this->finders['findBookPageFiles'], $book->getPath() . '/pages');

        $result = new NamedList();

        foreach ($files as $file) {
            $page = new BookPage($this->project, $this->renderer, $book, $file, true);

            if ($page->isLoaded()) {
                $result->add($page->getShortName(), $page);
            }
        }

        return $result;
    }

    /**
     * Return path to the folder where we expect to find videos.
     *
     * @param  string $locale
     * @return string
     */
    function getVideosPath($locale)
    {
        return $this->getLocalizedPath('videos', $locale);
    }

    /**
     * @var Video[]|NamedList
     */
    private $videos = false;

    /**
     * @param  string            $locale
     * @return Video[]|NamedList
     */
    function getVideos($locale = null)
    {
        if (empty($locale)) {
            $locale = $this->project->getDefaultLocale();
        }

        if ($this->videos === false) {
            $files = call_user_func($this->finders['findVideoFiles'], $this->getVideosPath($locale), $locale);

            $this->videos = new NamedList();

            foreach ($files as $file) {
                $video = new Video($this->project, $this->renderer, $file);

                if ($video->isLoaded()) {
                    $this->videos->add($video->getShortName(), $video);
                }
            }
        }

        return $this->videos;
    }

    /**
     * Return a video.
     *
     * @param  string      $name
     * @param  string|null $locale
     * @return Video|null
     */
    function getVideo($name, $locale = null)
    {
        foreach ($this->getVideos($locale) as $video) {
            if ($video->getShortName() === $name) {
                return $video;
            }
        }

        return null;
    }

    /**
     * Return path to the folder where we expect to find what's new articles.
     *
     * @param  string $locale
     * @return string
     */
    function getWhatsNewArticlesPath($locale)
    {
        return $this->getLocalizedPath('whats_new', $locale);
    }

    /**
     * @param  string|null                 $locale
     * @return WhatsNewArticle[]|NamedList
     */
    function getWhatsNewArticles($locale = null)
    {
        if (empty($locale)) {
            $locale = $this->project->getDefaultLocale();
        }

        $files = call_user_func($this->finders['findWhatsNewFiles'], $this->getWhatsNewArticlesPath($locale), $locale);

        $result = new NamedList();

        foreach ($files as $version_num => $version_files) {
            foreach ($version_files as $file) {
                $article = new WhatsNewArticle($this->project, $this->renderer, $version_num, $file);

                if ($article->isLoaded()) {
                    $result->add($article->getShortName(), $article);
                }
            }
        }

        return $result;
    }

    /**
     * @param  string                 $name
     * @param  string|null            $locale
     * @return WhatsNewArticle[]|null
     */
    function getWhatsNewArticle($name, $locale = null)
    {
        foreach ($this->getWhatsNewArticles($locale) as $article) {
            if ($article->getShortName() === $name) {
                return $article;
            }
        }

        return null;
    }

    /**
     * Return path to the folder where we expect to find release entries.
     *
     * @param  string $locale
     * @return string
     */
    function getReleasesPath($locale)
    {
        return $this->getLocalizedPath('releases', $locale);
    }

    /**
     * @param  string|null $locale
     * @return Release[]
     */
    function getReleases($locale = null)
    {
        if (empty($locale)) {
            $locale = $this->project->getDefaultLocale();
        }

        $files = call_user_func($this->finders['findReleaseFiles'], $this->getReleasesPath($locale), $locale);

        $result = [];

        foreach ($files as $version_number => $file) {
            $release = new Release($this->project, $this->renderer, $version_number, $file, true);

            if ($release->isLoaded()) {
                $result[$release->getVersionNumber()] = $release;
            }
        }

        return $result;
    }

    /**
     * @param  string $sub_dir
     * @param  string $locale
     * @return string
     */
    private function getLocalizedPath($sub_dir, $locale)
    {
        if ($this->project->isMultilingual()) {
            return $this->project->getPath() . "/$locale/$sub_dir";
        } else {
            return $this->project->getPath() . "/$sub_dir";
        }
    }
}
