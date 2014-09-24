<?php

  namespace ActiveCollab\Shade;

  use ActiveCollab\Shade\Element\Element, ActiveCollab\Shade\Element\Book, ActiveCollab\Shade\Element\BookPage, ActiveCollab\Shade\Element\Video, ActiveCollab\Shade\Element\WhatsNewArticle, ActiveCollab\Shade\Element\Release;
  use Smarty, Smarty_Internal_Template;
  use ActiveCollab\Shade, ActiveCollab\Shade\Error\ParamRequiredError;

  /**
   * Help element text helpers
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
     * Return current element
     *
     * @return Project
     */
    public static function &getCurrentProject()
    {
      return self::$current_project;
    }

    /**
     * Set current element
     *
     * @param Project $project
     */
    public static function setCurrentProject(Project $project)
    {
      self::$current_project = $project;
    }

    /**
     * @var Element|Book|BookPage|WhatsNewArticle|Release|Video
     */
    private static $current_element;

    /**
     * Return current element
     *
     * @return Element
     */
    public static function &getCurrentElement()
    {
      return self::$current_element;
    }

    /**
     * Set current element
     *
     * @param Element $element
     */
    public static function setCurrentElement(Element $element)
    {
      self::$current_element = $element;
    }

    /**
     * Reset current element and project
     */
    public static function resetCurrentElementAndProject()
    {
      self::$current_element = self::$current_project = null;
    }

    /**
     * Image function
     *
     * @param  array  $params
     * @param   Smarty_Internal_Template $smarty
     * @return string
     * @throws ParamRequiredError
     */
    public static function function_image($params, Smarty_Internal_Template &$smarty)
    {
      if (isset($params['name']) && $params['name']) {
        $page_level = self::$current_element->getPageLevel();

        if (self::$current_element instanceof BookPage) {
          $params = [ 'src' => self::pageLevelToPrefix($page_level) . "assets/images/books/" . self::$current_element->getBookName() . '/' . $params['name'] ];
        } elseif (self::$current_element instanceof WhatsNewArticle) {
          $params = [ 'src' => self::pageLevelToPrefix($page_level) . "assets/images/whats-new/" . self::$current_element->getVersionNumber() . '/' . $params['name'] ];
        } else {
          return '#';
        }

        return '<div class="center">' . Shade::htmlTag('img', $params) . '</div>';
      } else {
        throw new ParamRequiredError('name');
      }
    }

    /**
     * Return theme param URl
     *
     * @param array $params
     * @return string
     * @throws ParamRequiredError
     */
    public static function function_theme_asset($params)
    {
      $name = isset($params['name']) && $params['name'] ? ltrim($params['name'], '/') : null;
      $page_level = isset($params['page_level']) ? (integer) $params['page_level'] : 0;

      if (empty($name)) {
        throw new ParamRequiredError('name parameter is required');
      }

      return self::pageLevelToPrefix($page_level) . "assets/$name";
    }

    /**
     * @param $page_level
     * @return string
     */
    private static function pageLevelToPrefix($page_level)
    {
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
     * Render related video blokc
     *
     * @param  array  $params
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
          $result .= '<li><a href="' . self::function_video([ 'name' => $video->getShortName() ]) . '">' . Shade::clean($video->getTitle()) . '</a> <span class="play_time" title="' . Shade::lang('Video Play Time') . '">(' . Shade::clean($video->getPlayTime()) . ')</span>';

          if ($video->getDescription()) {
            $result .= ' &mdash; ' . Shade::clean($video->getDescription());
          }

          $result .= '</li>';
        }
      }

      if ($result) {
        return '<div class="related_videos"><h3>' . Shade::lang('Related Video') . '</h3><ul>' . $result . '</ul></div>';
      }

      return '';
    }
    
    public static function function_video($params)
    {
      if (isset($params['name']) && $params['name']) {
        return self::pageLevelToPrefix(self::$current_element->getPageLevel()) . 'videos/index.html#' . $params['name'];
      } else {
        throw new ParamRequiredError('name is required');
      }
    }

    /**
     * Link to a news article
     *
     * @param  array       $params
     * @param  string      $content
     * @param  Smarty      $smarty
     * @param  boolean     $repeat
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
        $params['href'] = $article->getUrl();

        return Shade::htmlTag('a', $params, $content);
      } else {
        return $content;
      }
    }

    /**
     * Link to a help book
     *
     * @param  array       $params
     * @param  string      $content
     * @param  Smarty      $smarty
     * @param  boolean     $repeat
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
        $params['href'] = $book->getUrl();

        if ($book->getDescription()) {
          $params['title'] = $book->getDescription();
        }

        return Shade::htmlTag('a', $params, $content);
      } else {
        return $content;
      }
    }

    /**
     * Link to a help book page
     *
     * @param  array       $params
     * @param  string      $content
     * @param  Smarty      $smarty
     * @param  boolean     $repeat
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
          $params['href'] = $page->getUrl();

          if (empty($params['class'])) {
            $params['class'] = 'link_to_help_book_page';
          } else {
            $params['class'] .= ' link_to_help_book_page';
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
        return '<span style="color: red; border-bottom: 1px dotted red; cursor: help;" title="Invalid page link: ' . Shade::clean($development_error_message) . '">' . Shade::clean($content) . '</span>';
      } else {
        return $content;
      }
    }

    /**
     * Link to a video
     *
     * @param  array       $params
     * @param  string      $content
     * @param  Smarty      $smarty
     * @param  boolean     $repeat
     * @return string|null
     * @throws ParamRequiredError
     */
    public static function block_video($params, $content, &$smarty, &$repeat)
    {
      if ($repeat) {
        return null;
      }

      $name = isset($params['name']) && $params['name'] ? $params['name'] : null;

      if (empty($name)) {
        throw new ParamRequiredError('name');
      }

      $video = self::getCurrentProject()->getVideo($name);

      if ($video instanceof Video) {
        $params['href'] = $video->getUrl();

        if ($video->getDescription()) {
          $params['title'] = $video->getDescription();
        }

        return Shade::htmlTag('a', $params, $content);
      } else {
        return $content;
      }
    }

    /**
     * Note block
     *
     * @param  array   $params
     * @param  string  $content
     * @param  Smarty  $smarty
     * @param  boolean $repeat
     * @return string
     */
    public static function block_note($params, $content, &$smarty, &$repeat)
    {
      if ($repeat) {
        return null;
      }

      $class = isset($params['class']) && $params['class'] ? $params['class'] : null;

      if (empty($class)) {
        $params['class'] = 'note';
      } else {
        $params['class'] .= ' note';
      }

      $title = isset($params['title']) && $params['title'] ? $params['title'] : null;

      if ($title) {
        $params['class'] .= ' with_title';

        return Shade::htmlTag('div', $params, function () use ($title, $content) {
          return '<h3>' . Shade::clean($title) . '</h3>' . Shade::markdownToHtml(trim($content));
        });
      } else {
        return Shade::htmlTag('div', $params, function () use ($content) {
          return Shade::markdownToHtml(trim($content));
        });
      }
    }

    /**
     * Option block
     *
     * @param  array   $params
     * @param  string  $content
     * @param  Smarty  $smarty
     * @param  boolean $repeat
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
     * Term block
     *
     * @param  array   $params
     * @param  string  $content
     * @param  Smarty  $smarty
     * @param  boolean $repeat
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
     * Wrap file system paths using this block
     *
     * @param  array   $params
     * @param  string  $content
     * @param  Smarty  $smarty
     * @param  boolean $repeat
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
     * Code block
     *
     * @param  array   $params
     * @param  string  $content
     * @param  Smarty  $smarty
     * @param  boolean $repeat
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

        if ($highlight === 'php') {
          $highlight = 'iphp';
        }

        if ($highlight === 'html' || $highlight === 'xhtml') {
          $highlight = 'xml';
        }

        return Shade::highlightCode($content, $highlight);
      }
    }

    /**
     * Render a page sub-header
     *
     * @param  array   $params
     * @param  string  $content
     * @param  Smarty  $smarty
     * @param  boolean $repeat
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

      return '<h3 id="s-' . Shade::clean($slug) . '" class="sub_header">' . Shade::clean($content) . ' <a href="#s-' . Shade::clean($slug) . '" title="' . Shade::lang('Link to this Section') . '" class="sub_permalink">#</a></h3>';
    }

    /**
     * Render a tutorial step
     *
     * @param  array   $params
     * @param  string  $content
     * @param  Smarty  $smarty
     * @param  boolean $repeat
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

  }
