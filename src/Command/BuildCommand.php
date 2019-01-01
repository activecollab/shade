<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use ActiveCollab\Shade\Element\Book;
use ActiveCollab\Shade\Element\BookPage;
use ActiveCollab\Shade\Element\Release;
use ActiveCollab\Shade\Element\Video;
use ActiveCollab\Shade\Element\WhatsNewArticle;
use ActiveCollab\Shade\Factory\ProjectFactory\ProjectFactoryInterface;
use ActiveCollab\Shade\ProjectInterface;
use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\SmartyHelpers;
use ActiveCollab\Shade\ThemeInterface;
use Exception;
use Smarty;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('build')
            ->addOption('target', null, InputOption::VALUE_REQUIRED, 'Where do you want Shade to build the help?')
            ->addOption('theme', null, InputOption::VALUE_REQUIRED, 'Name of the theme that should be used to build help')
            ->addOption('skip-books', null, InputOption::VALUE_REQUIRED, 'Comma separated list of books that should be skipped')
            ->setDescription('Build a help');
    }

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->getContainer()->get(ProjectFactoryInterface::class)->createProject(getcwd());

        if ($project->isValid()) {
            $target_path = $this->getBuildTarget($input, $project);
            $theme = $this->getTheme($input, $project);

            if (!$this->isValidTargetPath($target_path)) {
                $output->writeln("Build target '$target_path' not found or not writable");

                return;
            }

            if (!$theme instanceof ThemeInterface) {
                $output->writeln('Theme not found');

                return;
            }

            $this->smarty =& Shade::initSmarty($project, $theme);

            $this->prepareTargetPath($input, $output, $project, $target_path, $theme);

            foreach ($project->getLocales() as $locale => $locale_name) {
                SmartyHelpers::setCurrentLocale($locale);

                $this->smarty->assign('current_locale', $locale);

                foreach (['buildLandingPage', 'buildWhatsNew', 'buildReleaseNotes', 'buildBooks', 'buildVideos'] as $build_step) {
                    try {
                        if (!$this->$build_step($input, $output, $project, $target_path, $theme, $locale)) {
                            $output->writeln("Build process failed at step '$build_step'. Aborting...");

                            return;
                        }
                    } catch (Exception $e) {
                        $output->writeln('Exception: ' . $e->getMessage());
                        $output->writeln($e->getTraceAsString());
                    }
                }
            }
        } else {
            $output->writeln('<error>This is not a valid Shade project</error>');
        }
    }

    public function prepareTargetPath(InputInterface $input, OutputInterface $output, ProjectInterface $project, $target_path, ThemeInterface $theme)
    {
        Shade::clearDir($target_path, function ($path) use (&$output) {
            $output->writeln("$path deleted");
        });

        Shade::copyDir($theme->getPath() . '/assets', "$target_path/assets", function ($path) use (&$output) {
            $output->writeln("$path copied");
        });

        return true;
    }

    public function buildLandingPage(InputInterface $input, OutputInterface $output, ProjectInterface $project, $target_path, ThemeInterface $theme, $locale)
    {
        if ($locale === $project->getDefaultLocale()) {
            $index_path = "$target_path/index.html";
        } else {
            $index_path = "$target_path/$locale/index.html";

            Shade::createDir("$target_path/$locale", function ($path) use (&$output) {
                $output->writeln("Directory '$path' created");
            });

            Shade::createDir("$target_path/assets/images/$locale", function ($path) use (&$output) {
                $output->writeln("Directory '$path' created");
            });
        }

        $this->smarty->assign(
            [
                'common_questions' => $project->getCommonQuestions(),
                'page_level' => 0,
                'current_section' => 'home',
            ]
        );

        Shade::writeFile(
            $index_path,
            $this->smarty->fetch('index.tpl'),
            function ($path) use (&$output) {
                $output->writeln("File '$path' created");
            }
        );

        return true;
    }

    public function buildWhatsNew(InputInterface $input, OutputInterface $output, ProjectInterface $project, $target_path, ThemeInterface $theme, $locale)
    {
        $whats_new_path = $locale === $project->getDefaultLocale() ? "$target_path/whats-new" : "$target_path/$locale/whats-new";
        $whats_new_images_path = $locale === $project->getDefaultLocale() ? "$target_path/assets/images/whats-new" : "$target_path/assets/images/$locale/whats-new";

        Shade::createDir($whats_new_path, function ($path) use (&$output) {
            $output->writeln("Directory '$path' created");
        });

        Shade::createDir($whats_new_images_path, function ($path) use (&$output) {
            $output->writeln("Directory '$path' created");
        });

        $whats_new_articles = $project->getWhatsNewArticles($locale);

        foreach ($whats_new_articles as $whats_new_article) {
            $this->copyVersionImages($project, $whats_new_article, $target_path, $locale, $output);
        }

        $whats_new_articles_by_version = $this->getWhatsNewArticlesByVersion($whats_new_articles);

        $this->smarty->assign([
            'whats_new_articles' => $whats_new_articles,
            'whats_new_articles_by_version' => $whats_new_articles_by_version,
            'current_whats_new_article' => $this->getCurrentArticleFromSortedArticles($whats_new_articles_by_version),
            'page_level' => 1,
            'current_section' => 'whats_new',
        ]);

        Shade::writeFile("$whats_new_path/index.html", $this->smarty->fetch('whats_new_article.tpl'), function ($path) use (&$output) {
            $output->writeln("File '$path' created");
        });

        Shade::writeFile("$whats_new_path/rss.xml", $this->smarty->fetch('rss.tpl'), function ($path) use (&$output) {
            $output->writeln("File '$path' created");
        });

        foreach ($whats_new_articles as $whats_new_article) {
            $this->smarty->assign('current_whats_new_article', $whats_new_article);

            Shade::writeFile("$whats_new_path/" . $whats_new_article->getShortName() . '.html', $this->smarty->fetch('whats_new_article.tpl'), function ($path) use (&$output) {
                $output->writeln("File '$path' created");
            });
        }

        return true;
    }

    private function copyVersionImages(ProjectInterface $project, WhatsNewArticle $article, $target_path, $locale, OutputInterface $output)
    {
        $version_num = $article->getVersionNumber();

        $version_dir_path = $locale === $project->getDefaultLocale() ? "$target_path/assets/images/whats-new/$version_num" : "$target_path/assets/images/$locale/whats-new/$version_num";

        if (is_dir($version_dir_path)) {
            return;
        }

        $version_path = dirname($article->getIndexFilePath());

        if (is_dir("$version_path/images")) {
            Shade::copyDir("$version_path/images", $version_dir_path, null, function ($path) use (&$output) {
                $output->writeln("$path copied");
            });
        }
    }

    private function getWhatsNewArticlesByVersion($whats_new_articles)
    {
        $whats_new_articles_by_version = [];

        foreach ($whats_new_articles as $whats_new_article) {
            if (empty($whats_new_articles_by_version[$whats_new_article->getVersionNumber()])) {
                $whats_new_articles_by_version[$whats_new_article->getVersionNumber()] = [];
            }

            $whats_new_articles_by_version[$whats_new_article->getVersionNumber()][] = $whats_new_article;
        }

        uksort($whats_new_articles_by_version, function ($a, $b) {
            return version_compare($b, $a);
        });

        return $whats_new_articles_by_version;
    }

    private function getCurrentArticleFromSortedArticles($whats_new_articles_by_version)
    {
        foreach ($whats_new_articles_by_version as $v => $articles) {
            foreach ($articles as $article) {
                return $article;
            }
        }

        return null;
    }

    public function buildReleaseNotes(InputInterface $input, OutputInterface $output, ProjectInterface $project, $target_path, ThemeInterface $theme, $locale)
    {
        $release_notes_path = $locale === $project->getDefaultLocale() ? "$target_path/release-notes" : "$target_path/$locale/release-notes";

        Shade::createDir($release_notes_path, function ($path) use (&$output) {
            $output->writeln("Directory '$path' created");
        });

        $releases = $project->getReleases($locale);
        $releases_by_major_version = $this->getReleasesByMajorVersion($releases);

        $this->smarty->assign([
            'releases_by_major_version' => $releases_by_major_version,
            'current_release' => $this->getCurrentReleaseFromSortedReleases($releases_by_major_version),
            'page_level' => 1,
            'current_section' => 'releases',
        ]);

        Shade::writeFile("$release_notes_path/index.html", $this->smarty->fetch('release.tpl'), function ($path) use (&$output) {
            $output->writeln("File '$path' created");
        });

        foreach ($releases as $release) {
            $this->smarty->assign([
                'current_release' => $release,
            ]);

            Shade::writeFile("$release_notes_path/" . $release->getSlug() . '.html', $this->smarty->fetch('release.tpl'), function ($path) use (&$output) {
                $output->writeln("File '$path' created");
            });
        }

        return true;
    }

    /**
     * Return releases by major version.
     *
     * @param  Release[] $releases
     * @return array
     */
    private function getReleasesByMajorVersion($releases)
    {
        $result = [];

        foreach ($releases as $release) {
            $bits = explode('.', $release->getVersionNumber());

            while (count($bits) > 2) {
                array_pop($bits);
            }

            $major_version = implode('.', $bits);

            if (empty($result[$major_version])) {
                $result[$major_version] = [];
            }

            $result[$major_version][] = $release;
        }

        return $result;
    }

    /**
     * @param  array        $sorted_releases
     * @return Release|null
     */
    private function getCurrentReleaseFromSortedReleases($sorted_releases)
    {
        foreach ($sorted_releases as $releases) {
            foreach ($releases as $release) {
                return $release;
            }
        }

        return null;
    }

    public function buildBooks(InputInterface $input, OutputInterface $output, ProjectInterface $project, $target_path, ThemeInterface $theme, $locale)
    {
        $books_path = $locale === $project->getDefaultLocale() ? "$target_path/books" : "$target_path/$locale/books";
        $books_images_path = $locale === $project->getDefaultLocale() ? "$target_path/assets/images/books" : "$target_path/assets/images/$locale/books";

        Shade::createDir($books_path, function ($path) use (&$output) {
            $output->writeln("Directory '$path' created");
        });

        Shade::createDir($books_images_path, function ($path) use (&$output) {
            $output->writeln("Directory '$path' created");
        });

        $books = $project->getBooks($locale);

        $this->skipBooks($input, $books);

        foreach ($books as $book) {
            $this->copyBookImages($project, $book, $target_path, $locale, $output);
        }

        $this->smarty->assign([
            'books' => $books,
            'page_level' => 1,
            'current_section' => 'books',
        ]);

        Shade::writeFile("$books_path/index.html", $this->smarty->fetch('books.tpl'), function ($path) use (&$output) {
            $output->writeln("File '$path' created");
        });

        foreach ($books as $book) {
            Shade::createDir("$books_path/" . $book->getShortName(), function ($path) use (&$output) {
                $output->writeln("Directory '$path' created");
            });

            $pages = $book->getPages();

            $this->smarty->assign([
                'current_book' => $book,
                'page_level' => 2,
                'pages' => $pages,
                'current_page' => $this->getCurrentPage($pages),
                'sidebar_image' => '../../assets/images/books/' . $book->getShortName() . '/_cover_small.png',
            ]);

            Shade::writeFile("$books_path/" . $book->getShortName() . '/index.html', $this->smarty->fetch('book_page.tpl'), function ($path) use (&$output) {
                $output->writeln("File '$path' created");
            });

            foreach ($pages as $page) {
                $this->smarty->assign([
                    'current_page' => $page,
                ]);

                Shade::writeFile("$books_path/" . $book->getShortName() . '/' . $page->getShortName() . '.html', $this->smarty->fetch('book_page.tpl'), function ($path) use (&$output) {
                    $output->writeln("File '$path' created");
                });
            }
        }

        return true;
    }

    /**
     * @param InputInterface $input
     * @param array          $books
     */
    private function skipBooks(InputInterface $input, array &$books)
    {
        $skip_books = $input->getOption('skip-books');

        if ($skip_books) {
            foreach (explode(',', $skip_books) as $book_to_skip) {
                if (isset($books[$book_to_skip])) {
                    unset($books[$book_to_skip]);
                }
            }
        }
    }

    private function copyBookImages(ProjectInterface $project, Book $book, $target_path, $locale, OutputInterface $output)
    {
        $book_path = $book->getPath();
        $book_name = $book->getShortName();

        if (is_dir("$book_path/images")) {
            $book_images_path = $locale === $project->getDefaultLocale() ? "$target_path/assets/images/books/$book_name" : "$target_path/assets/images/$locale/books/$book_name";

            Shade::copyDir("$book_path/images", $book_images_path, null, function ($path) use (&$output) {
                $output->writeln("$path copied");
            });
        }
    }

    /**
     * @param  BookPage[] $pages
     * @return BookPage
     */
    private function getCurrentPage($pages)
    {
        foreach ($pages as $page) {
            return $page;
        }

        return null;
    }

    public function buildVideos(InputInterface $input, OutputInterface $output, ProjectInterface $project, $target_path, ThemeInterface $theme, $locale)
    {
        $videos_path = $locale === $project->getDefaultLocale() ? "$target_path/videos" : "$target_path/$locale/videos";

        Shade::createDir($videos_path, function ($path) use (&$output) {
            $output->writeln("Directory '$path' created");
        });

        if (is_dir($project->getFinder()->getVideosPath($locale) . '/images')) {
            $video_images_path = $locale === $project->getDefaultLocale() ? "$target_path/assets/images/videos" : "$target_path/assets/images/$locale/videos";

            Shade::createDir($video_images_path, function ($path) use (&$output) {
                $output->writeln("Directory '$path' created");
            });

            Shade::copyDir($project->getFinder()->getVideosPath($locale) . '/images', $video_images_path, null, function ($path) use (&$output) {
                $output->writeln("$path copied");
            });
        }

        $videos = $project->getVideos();

        if (!$videos->count()) {
            return true; // Skip video section rendering
        }

        $video_groups = $project->getVideoGroups();

        $this->smarty->assign([
            'video_groups' => $video_groups,
            'videos' => $videos,
            'page_level' => 1,
            'video_player' => $project->getVideoPlayer(),
            'current_video' => $this->getCurrentVideo($video_groups, $videos),
            'current_section' => 'videos',
        ]);

        Shade::writeFile("$videos_path/index.html", $this->smarty->fetch('videos.tpl'), function ($path) use (&$output) {
            $output->writeln("File '$path' created");
        });

        foreach ($videos as $video) {
            $this->smarty->assign([
                'current_video' => $video,
            ]);

            Shade::writeFile("$videos_path/" . $video->getSlug() . '.html', $this->smarty->fetch('videos.tpl'), function ($path) use (&$output) {
                $output->writeln("File '$path' created");
            });
        }

        return true;
    }

    /**
     * @param  string[]   $video_groups
     * @param  Video[]    $videos
     * @return Video|null
     */
    private function getCurrentVideo($video_groups, $videos)
    {
        foreach ($video_groups as $video_group => $video_group_caption) {
            foreach ($videos as $video) {
                if ($video->getGroupName() === $video_group) {
                    return $video;
                }
            }
        }

        return null;
    }

    private function getBuildTarget(InputInterface $input, ProjectInterface &$project): string
    {
        $target = $input->getOption('target');

        if (empty($target)) {
            $target = $project->getDefaultBuildTarget();
        }

        return (string) $target;
    }

    private function isValidTargetPath(string $target_path): bool
    {
        return $target_path && is_dir($target_path);
    }

    private function getTheme(InputInterface $input, ProjectInterface &$project): ThemeInterface
    {
        return $project->getBuildTheme($input->getOption('theme'));
    }
}
