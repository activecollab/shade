<?php

  namespace ActiveCollab\Shade;

  use Smarty;
  use ActiveCollab\Shade, Shade\Element\Element, ActiveCollab\Shade\Error\ParamRequiredError;

  /**
   * Help element text helpers
   *
   * @package Shade
   */
  class SmartyHelpers
  {
    /**
     * Current help element
     *
     * @var Element
     */
    private static $current_element;

    /**
     * Return current element
     *
     * @return Element
     */
    public static function getCurrentElement()
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
     * Image function
     *
     * @param  array  $params
     * @return string
     * @throws ParamRequiredError
     */
    public static function function_image($params)
    {
      if (isset($params['name']) && $params['name']) {
        return Shade::getImageUrl(static::getCurrentElement(), strtolower($params['name']));
      } else {
        throw new ParamRequiredError('name');
      }
    }

    /**
     * Render related video blokc
     *
     * @param  array  $params
     * @param  Smarty $smarty
     * @return string
     */
    public static function function_related_video($params, &$smarty)
    {
      $names = explode(',', array_required_var($params, 'name'));

      $result = '';

      $all_videos = AngieApplication::help()->getVideos(\Angie\Authentication::getLoggedUser());

      foreach ($names as $name) {
        $video = $all_videos->get($name);

        // Check if we have a video instance. If not, ignore (don't break the system in case of a missing video)
        if ($video instanceof HelpVideo) {
          $result .= '<li><a href="' . Shade::clean($video->getUrl()) . '">' . Shade::clean($video->getTitle()) . '</a> <span class="play_time" title="' . lang('Video Play Time') . '">(' . Shade::clean($video->getPlayTime()) . ')</span>';

          if ($video->getDescription()) {
            $result .= ' &mdash; ' . Shade::clean($video->getDescription());
          }

          $result .= '</li>';
        }
      }

      if ($result) {
        return '<div class="related_videos"><h3>' . lang('Related Video') . '</h3><ul>' . $result . '</ul></div>';
      }

      return '';
    }

    /**
     * Link to a news article
     *
     * @param  array       $params
     * @param  string      $content
     * @param  Smarty      $smarty
     * @param  boolean     $repeat
     * @return string|null
     */
    public static function block_news_article($params, $content, &$smarty, &$repeat)
    {
      if ($repeat) {
        return null;
      }

      $name = array_required_var($params, 'name', true);

      $article = AngieApplication::help()->getWhatsNew(\Angie\Authentication::getLoggedUser())->get($name);

      if ($article instanceof HelpWhatsNewArticle) {
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
     */
    public static function block_book($params, $content, &$smarty, &$repeat)
    {
      if ($repeat) {
        return null;
      }

      $name = array_required_var($params, 'name', true);

      $book = AngieApplication::help()->getBooks(\Angie\Authentication::getLoggedUser())->get($name);

      if ($book instanceof HelpBook) {
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
     */
    public static function block_page($params, $content, &$smarty, &$repeat)
    {
      if ($repeat) {
        return null;
      }

      $name = array_required_var($params, 'name', true);
      $book_name = array_var($params, 'book', null, true);

      if (empty($book_name)) {
        if (self::$current_element instanceof HelpBook) {
          $book_name = self::$current_element->getShortName();
        } elseif (self::$current_element instanceof HelpBookPage) {
          $book_name = self::$current_element->getBookName();
        }
      }

      $user = \Angie\Authentication::getLoggedUser();

      $book = $book_name ? AngieApplication::help()->getBooks($user)->get($book_name) : null;

      if ($book instanceof HelpBook) {
        $page = $book->getPages($user)->get($name);

        if ($page instanceof HelpBookPage) {
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

      if (AngieApplication::isInDevelopment() && isset($development_error_message)) {
        return '<span style="color: red; border-bottom: 1px dotted red; cursor: help;" title="Invalid page link: ' . Shade::clean($development_error_message) . '">' . Shade::clean($content) . '</span>';
      } else {
        return $content;
      }
    } // block_page

    /**
     * Link to a video
     *
     * @param  array       $params
     * @param  string      $content
     * @param  Smarty      $smarty
     * @param  boolean     $repeat
     * @return string|null
     */
    public static function block_video($params, $content, &$smarty, &$repeat)
    {
      if ($repeat) {
        return null;
      }

      $name = array_required_var($params, 'name', true);

      $video = AngieApplication::help()->getVideos(\Angie\Authentication::getLoggedUser())->get($name);

      if ($video instanceof HelpVideo) {
        $params['href'] = $video->getUrl();

        if ($video->getDescription()) {
          $params['title'] = $video->getDescription();
        }

        return Shade::htmlTag('a', $params, $content);
      } else {
        return $content;
      }
    } // block_video

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

      return '<h3 id="s-' . Shade::clean($slug) . '" class="sub_header">' . Shade::clean($content) . ' <a href="#s-' . Shade::clean($slug) . '" title="' . lang('Link to this Section') . '" class="sub_permalink">#</a></h3>';
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
