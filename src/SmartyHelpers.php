<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Element\Book;
use ActiveCollab\Shade\Element\BookPage;
use ActiveCollab\Shade\Element\Element;
use ActiveCollab\Shade\Element\ElementInterface;
use ActiveCollab\Shade\Element\Release;
use ActiveCollab\Shade\Element\Video;
use ActiveCollab\Shade\Element\WhatsNewArticle;
use ActiveCollab\Shade\Error\ParamRequiredError;
use ActiveCollab\Shade\Project\Project;
use ActiveCollab\Shade\Project\ProjectInterface;
use Exception;
use Smarty;

/**
 * Help element text helpers.
 *
 * @package Shade
 */
class SmartyHelpers
{
    /**
     * @var Project
     */
    private static $current_project;

    /**
     * Return current element.
     *
     * @return Project
     */
    public static function &getCurrentProject()
    {
        return self::$current_project;
    }

    public static function setCurrentProject(ProjectInterface $project)
    {
        if ($project instanceof Project) {
            self::$current_project = $project;
        } else {
            throw new ParamRequiredError('project');
        }
    }

    /**
     * @var Element|Book|BookPage|WhatsNewArticle|Release|Video
     */
    private static $current_element;

    /**
     * Return current element.
     *
     * @return Element
     */
    public static function &getCurrentElement()
    {
        return self::$current_element;
    }

    /**
     * Set current element.
     *
     * @param  ProjectInterface|ElementInterface|BuildableInterface $element
     * @throws Exception
     */
    public static function setCurrentElement($element)
    {
        if ($element instanceof ElementInterface || $element instanceof ProjectInterface) {
            self::$current_element = $element;
        } else {
            throw new Exception('Projects and elements are accepted as current element');
        }
    }

    /**
     * Reset current element and project.
     */
    public static function resetCurrentElementAndProject()
    {
        self::$current_element = null;
        self::$current_project = null;
    }

    /**
     * @var string
     */
    private static $default_locale;

    /**
     * @param string $locale
     */
    public static function setDefaultLocale($locale)
    {
        self::$default_locale = $locale;
    }

    /**
     * @var string
     */
    private static $current_locale;

    /**
     * @param string $locale
     */
    public static function setCurrentLocale($locale)
    {
        self::$current_locale = $locale;
    }

    /**
     * Image function.
     *
     * @param  array              $params
     * @return string
     * @throws ParamRequiredError
     */
    public static function function_image($params)
    {
        if (isset($params['name']) && $params['name']) {
            $page_level = self::$current_element->getPageLevel();

            if (self::$current_element instanceof BookPage) {
                $params = ['src' => self::getBookPageImageUrl($params['name'], $page_level)];
            } elseif (self::$current_element instanceof WhatsNewArticle) {
                $params = ['src' => self::getWhatsNewArticleImageUrl($params['name'], $page_level)];
            } else {
                return '#';
            }

            return '<div class="center">' . Shade::htmlTag('img', $params) . '</div>';
        } else {
            throw new ParamRequiredError('name');
        }
    }

    /**
     * @param  string $name
     * @param  int    $page_level
     * @return string
     */
    private static function getBookPageImageUrl($name, $page_level)
    {
        return self::$current_locale === self::$default_locale ?
            self::pageLevelToPrefix($page_level, self::$current_locale) . 'assets/images/books/' . self::$current_element->getBookName() . '/' . $name :
            self::pageLevelToPrefix($page_level, self::$current_locale) . 'assets/images/' . self::$current_locale . '/books/' . self::$current_element->getBookName() . '/' . $name;
    }

    /**
     * @param  string $name
     * @param  int    $page_level
     * @return string
     */
    private static function getWhatsNewArticleImageUrl($name, $page_level)
    {
        return $src = self::$current_locale === self::$default_locale ?
            self::pageLevelToPrefix($page_level, self::$current_locale) . 'assets/images/whats-new/' . self::$current_element->getVersionNumber() . '/' . $name :
            self::pageLevelToPrefix($page_level, self::$current_locale) . 'assets/images/' . self::$current_locale . '/whats-new/' . self::$current_element->getVersionNumber() . '/' . $name;
    }

    /**
     * Return theme param URL.
     *
     * @param  array              $params
     * @return string
     * @throws ParamRequiredError
     */
    public static function function_theme_asset($params)
    {
        $name = isset($params['name']) && $params['name'] ? ltrim($params['name'], '/') : null;
        $page_level = isset($params['page_level']) ? (integer) $params['page_level'] : 0;
        $current_locale = isset($params['current_locale']) ? $params['current_locale'] : self::$default_locale;

        if (empty($name)) {
            throw new ParamRequiredError('name parameter is required');
        }

        return self::pageLevelToPrefix($page_level, $current_locale) . "assets/$name";
    }

    /**
     * @param  array  $params
     * @return string
     */
    public static function function_asset_link($params)
    {
        $name = isset($params['name']) ? $params['name'] : '';
        $page_level = isset($params['page_level']) ? (integer) $params['page_level'] : 0;
        $locale = isset($params['locale']) && $params['locale'] ? $params['locale'] : null;

        return self::pageLevelToPrefix($page_level, $locale) . "assets/$name";
    }

    /**
     * @param  array  $params
     * @return string
     */
    public static function function_stylesheet_url($params)
    {
        $page_level = isset($params['page_level']) ? (integer) $params['page_level'] : 0;
        $locale = isset($params['locale']) && $params['locale'] ? $params['locale'] : null;

        return '<link rel="stylesheet" type="text/css" href="' . self::pageLevelToPrefix($page_level, $locale) . 'assets/stylesheets/main.css?timestamp=' . time() . '">';
    }

    /**
     * @param  int         $page_level
     * @param  string|null $locale
     * @return string
     */
    private static function pageLevelToPrefix($page_level, $locale = null)
    {
        if ($locale && $locale != self::$default_locale) {
            $page_level++;
        }

        if ($page_level > 0) {
            $prefix = './';

            for ($i = 0; $i < $page_level; $i++) {
                $prefix .= '../';
            }

            return $prefix;
        } else {
            return '';
        }
    }

    /**
     * Render related video blokc.
     *
     * @param  array              $params
     * @return string
     * @throws ParamRequiredError
     */
    public static function function_related_video($params)
    {
        $names = isset($params['name']) ? explode(',', $params['name']) : null;

        if (empty($names)) {
            throw new ParamRequiredError('name parameter is required');
        }

        $result = '';

        $all_videos = self::getCurrentProject()->getVideos();

        foreach ($names as $name) {
            $video = $all_videos->get($name);

            // Check if we have a video instance. If not, ignore (don't break the system in case of a missing video)
            if ($video instanceof Video) {
                $result .= '<li><a href="' . self::getVideoUrl($video->getShortName()) . '">' . Shade::clean($video->getTitle()) . '</a> <span class="play_time" title="' . Shade::lang('Video Play Time') . '">(' . Shade::clean($video->getPlayTime()) . ')</span>';

                if ($video->getDescription()) {
                    $result .= ' &mdash; ' . Shade::clean($video->getDescription());
                }

                $result .= '</li>';
            }
        }

        if ($result) {
            return '<div class="related_videos"><h2>' . Shade::lang('Related Video') . '</h2><ul>' . $result . '</ul></div>';
        }

        return '';
    }

    /**
     * Link to a video.
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|null
     * @throws ParamRequiredError
     */
    public static function block_video($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        $name = isset($params['name']) ? $params['name'] : null;

        if (empty($name)) {
            throw new ParamRequiredError('name is required');
        }

        $video = self::getCurrentProject()->getVideo($name);

        if ($video instanceof Video) {
            return Shade::htmlTag('a', [
                'href' => self::getVideoUrl($video->getSlug()),
                'title' => $video->getDescription() ? $video->getDescription() : null,
            ], $content);
        } else {
            return $content;
        }
    }

    /**
     * Return video URL.
     *
     * @param  string $slug
     * @return string
     */
    private static function getVideoUrl($slug)
    {
        return self::pageLevelToPrefix(self::$current_element->getPageLevel()) . 'videos/' . $slug . '.html';
    }

    /**
     * Link to a news article.
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|null
     * @throws ParamRequiredError
     */
    public static function block_news_article($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        $name = isset($params['name']) ? $params['name'] : null;

        if (empty($name)) {
            throw new ParamRequiredError('name is required');
        }

        $article = self::getCurrentProject()->getWhatsNewArticle($name);

        if ($article instanceof WhatsNewArticle) {
            return Shade::htmlTag('a', [
                'href' => self::getWhatsNewArticleUrl($article->getSlug()),
            ], $content);
        } else {
            return $content;
        }
    }

    /**
     * Return what's new article URL.
     *
     * @param  string $slug
     * @return string
     */
    private static function getWhatsNewArticleUrl($slug)
    {
        return self::pageLevelToPrefix(self::$current_element->getPageLevel()) . 'whats-new/' . $slug . '.html';
    }

    /**
     * Link to a help book.
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|null
     * @throws ParamRequiredError
     */
    public static function block_book($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        $name = isset($params['name']) && $params['name'] ? $params['name'] : null;

        if (empty($name)) {
            throw new ParamRequiredError('name');
        }

        $book = self::getCurrentProject()->getBook($name);

        if ($book instanceof Book) {
            $params['href'] = self::getBookUrl($book->getShortName());

            if ($book->getDescription()) {
                $params['title'] = $book->getDescription();
            }

            return Shade::htmlTag('a', $params, $content);
        } else {
            return $content;
        }
    }

    /**
     * Return book URL.
     *
     * @param  string $short_name
     * @return string
     */
    private static function getBookUrl($short_name)
    {
        return self::pageLevelToPrefix(self::$current_element->getPageLevel()) . 'books/' . $short_name . '/index.html';
    }

    /**
     * Link to a help book page.
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|null
     * @throws ParamRequiredError
     */
    public static function block_page($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        $name = isset($params['name']) && $params['name'] ? $params['name'] : null;

        if (empty($name)) {
            throw new ParamRequiredError('name');
        }

        $book_name = isset($params['book']) ? $params['book'] : null;
        $section = isset($params['section']) && $params['section'] ? '#s-' . Shade::slug($params['section']) : null;

        if (empty($book_name)) {
            if (self::$current_element instanceof Book) {
                $book_name = self::$current_element->getShortName();
            } elseif (self::$current_element instanceof BookPage) {
                $book_name = self::$current_element->getBookName();
            }
        }

        $book = $book_name ? self::getCurrentProject()->getBook($book_name) : null;

        if ($book instanceof Book) {
            $page = $book->getPage($name);

            if ($page instanceof BookPage) {
                $params['href'] = self::getBookPageUrl($book->getShortName(), $page->getShortName()) . $section;

                if (empty($params['class'])) {
                    $params['class'] = 'link_to_book_page';
                } else {
                    $params['class'] .= ' link_to_book_page';
                }

                $params['data-page-name'] = $page->getShortName();
                $params['data-book-name'] = $book->getShortName();

                return Shade::htmlTag('a', $params, $content);
            } else {
                $development_error_message = 'Page not found';
            }
        } else {
            $development_error_message = 'Book not found';
        }

        if (Shade::isTesting() && isset($development_error_message)) {
            return '<span style="color: red; border-bottom: 1px dotted red; cursor: help;" title="Invalid page link: ' . Shade::clean($development_error_message) . '">' . $content . '</span>';
        } else {
            return $content;
        }
    }

    /**
     * Link to a help book page.
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|void
     * @throws ParamRequiredError
     */
    public static function block_todo($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return;
        }

        Shade::recordTodo(trim($content), self::getCurrentElement());

        return '<div class="panel panel-danger"><div class="panel-heading">To-do</div><div class="panel-body">' . $content . '</div></div>';
    }

    /**
     * Return book page URL.
     *
     * @param  string $book_name
     * @param  string $page_slug
     * @return string
     */
    private static function getBookPageUrl($book_name, $page_slug)
    {
        return self::pageLevelToPrefix(self::$current_element->getPageLevel()) . 'books/' . $book_name . '/' . $page_slug . '.html';
    }

    /**
     * Note block.
     *
     * @param  array  $params
     * @param  string $content
     * @param  Smarty $smarty
     * @param  bool   $repeat
     * @return string
     */
    public static function block_note($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        $title = isset($params['title']) && $params['title'] ? $params['title'] : null;

        if (empty($title)) {
            $title = 'Note';
        }

        return '<div class="note panel panel-warning"><div class="panel-heading">' . Shade::clean($title) . '</div><div class="panel-body">' . Shade::markdownToHtml(trim($content)) . '</div></div>';
    }

    /**
     * Option block.
     *
     * @param  array  $params
     * @param  string $content
     * @param  Smarty $smarty
     * @param  bool   $repeat
     * @return string
     */
    public static function block_option($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        if (empty($params['class'])) {
            $params['class'] = 'outlined_inline option';
        } else {
            $params['class'] .= ' outlined_inline option';
        }

        return Shade::htmlTag('span', $params, function () use ($content) {
            return Shade::clean(trim($content));
        });
    }

    /**
     * Term block.
     *
     * @param  array  $params
     * @param  string $content
     * @param  Smarty $smarty
     * @param  bool   $repeat
     * @return string
     */
    public static function block_term($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        if (empty($params['class'])) {
            $params['class'] = 'outlined_inline term';
        } else {
            $params['class'] .= ' outlined_inline term';
        }

        return Shade::htmlTag('span', $params, function () use ($content) {
            return Shade::clean(trim($content));
        });
    }

    /**
     * Wrap file system paths using this block.
     *
     * @param  array  $params
     * @param  string $content
     * @param  Smarty $smarty
     * @param  bool   $repeat
     * @return string
     */
    public static function block_path($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        if (empty($params['class'])) {
            $params['class'] = 'outlined_inline outlined_inline_mono path';
        } else {
            $params['class'] .= ' outlined_inline outlined_inline_mono path';
        }

        return Shade::htmlTag('span', $params, function () use ($content) {
            return Shade::clean(trim($content));
        });
    }

    /**
     * Code block.
     *
     * @param  array  $params
     * @param  string $content
     * @param  Smarty $smarty
     * @param  bool   $repeat
     * @return string
     */
    public static function block_code($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        $content = trim($content); // Remove whitespace

        if (array_key_exists('inline', $params)) {
            $inline = isset($params['inline']) && $params['inline'];
        } else {
            $inline = strpos($content, "\n") === false;
        }

        if ($inline) {
            if (empty($params['class'])) {
                $params['class'] = 'outlined_inline outlined_inline_mono inline_code';
            } else {
                $params['class'] .= ' outlined_inline outlined_inline_mono inline_code';
            }

            return Shade::htmlTag('span', $params, function () use ($content) {
                return Shade::clean(trim($content));
            });
        } else {
            $highlight = isset($params['highlight']) && $params['highlight'] ? $params['highlight'] : null;

            if ($highlight === 'html' || $highlight === 'xhtml') {
                $highlight = 'xml';
            }

            return Shade::highlightCode($content, $highlight);
        }
    }

    /**
     * Render a page sub-header.
     *
     * @param  array  $params
     * @param  string $content
     * @param  Smarty $smarty
     * @param  bool   $repeat
     * @return string
     */
    public static function block_sub($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        $slug = isset($params['slug']) ? $params['slug'] : null;

        if (empty($slug)) {
            $slug = Shade::slug($content);
        }

        return '<h2 id="s-' . Shade::clean($slug) . '" class="sub_header">' . Shade::clean($content) . ' <a href="#s-' . Shade::clean($slug) . '" title="' . Shade::lang('Link to this Section') . '" class="sub_permalink">#</a></h2>';
    }

    /**
     * Render a tutorial step.
     *
     * @param  array  $params
     * @param  string $content
     * @param  Smarty $smarty
     * @param  bool   $repeat
     * @return string
     */
    public static function block_step($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        $num = isset($params['num']) ? (integer) $params['num'] : null;

        if (empty($num)) {
            $num = 1;
        }

        return '<div class="step step-' . $num . '">
    <div class="step_num"><span>' . $num . '</span></div>
    <div class="step_content">' . Shade::markdownToHtml(trim($content)) . '</div>
  </div>';
    }

    /**
     * Render navigation link.
     *
     * @param  array  $params
     * @return string
     */
    public static function function_navigation_link($params)
    {
        $page_level = isset($params['page_level']) && (integer) $params['page_level'] > 0 ? (integer) $params['page_level'] : 0;
        $section = isset($params['section']) ? $params['section'] : null;

        switch ($section) {
            case 'books':
                return self::pageLevelToPrefix($page_level) . 'books/index.html';
            case 'releases':
                return self::pageLevelToPrefix($page_level) . 'release-notes/index.html';
            case 'whats_new':
                return self::pageLevelToPrefix($page_level) . 'whats-new/index.html';
            case 'videos':
                return self::pageLevelToPrefix($page_level) . 'videos/index.html';
            default:
                return self::pageLevelToPrefix($page_level) . 'index.html';
        }
    }

    /**
     * Render navigation link.
     *
     * @param  array              $params
     * @return string
     * @throws ParamRequiredError
     */
    public static function function_locale_link($params)
    {
        $code = isset($params['locale']) && $params['locale'] ? $params['locale'] : null;

        if (empty($code)) {
            throw new ParamRequiredError('locale');
        }

        $default_locale = isset($params['default_locale']) && $params['default_locale'] ? $params['default_locale'] : 'en';
        $current_locale = isset($params['current_locale']) && $params['current_locale'] ? $params['current_locale'] : $default_locale;
        $page_level = isset($params['page_level']) && (integer) $params['page_level'] > 0 ? (integer) $params['page_level'] : 0;

        if ($code === $default_locale) {
            return self::pageLevelToPrefix($page_level, $current_locale) . 'index.html';
        } else {
            return self::pageLevelToPrefix($page_level, $current_locale) . "{$code}/index.html";
        }
    }

    /**
     * Render added block in release notes.
     *
     * Note: This block is available only in release notes!
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|void
     * @throws ParamRequiredError
     * @throws Exception
     */
    public static function block_added($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return;
        }

        if (self::$current_element instanceof Release) {
            return '<p>Added:</p>' . Shade::markdownToHtml(trim($content));
        } else {
            throw new Exception('added block is available for release notes only');
        }
    }

    /**
     * Render deprecated block in release notes.
     *
     * Note: This block is available only in release notes!
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|void
     * @throws ParamRequiredError
     * @throws Exception
     */
    public static function block_deprecated($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return;
        }

        if (self::$current_element instanceof Release) {
            return '<p>Deprecated:</p>' . Shade::markdownToHtml(trim($content));
        } else {
            throw new Exception('added block is available for release notes only');
        }
    }

    /**
     * Render removed block in release notes.
     *
     * Note: This block is available only in release notes!
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|void
     * @throws ParamRequiredError
     * @throws Exception
     */
    public static function block_removed($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return;
        }

        if (self::$current_element instanceof Release) {
            return '<p>Removed:</p>' . Shade::markdownToHtml(trim($content));
        } else {
            throw new Exception('added block is available for release notes only');
        }
    }

    /**
     * Render fixed block in release notes.
     *
     * Note: This block is available only in release notes!
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|void
     * @throws ParamRequiredError
     * @throws Exception
     */
    public static function block_fixed($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return;
        }

        if (self::$current_element instanceof Release) {
            return '<p>Fixed:</p>' . Shade::markdownToHtml(trim($content));
        } else {
            throw new Exception('added block is available for release notes only');
        }
    }

    /**
     * Render security block in release notes.
     *
     * Note: This block is available only in release notes!
     *
     * @param  array              $params
     * @param  string             $content
     * @param  Smarty             $smarty
     * @param  bool               $repeat
     * @return string|null
     * @throws ParamRequiredError
     * @throws Exception
     */
    public static function block_security($params, $content, &$smarty, &$repeat)
    {
        if ($repeat) {
            return null;
        }

        if (self::$current_element instanceof Release) {
            return '<p>Security:</p>' . Shade::markdownToHtml(trim($content));
        } else {
            throw new Exception('added block is available for release notes only');
        }
    }

    /**
     * @var string
     */
    private static $shade_version = false;

    /**
     * Return shade version.
     */
    public static function function_shade_version()
    {
        if (self::$shade_version === false) {
            self::$shade_version = trim(file_get_contents(dirname(__DIR__) . '/VERSION'));
        }

        return self::$shade_version;
    }
}
