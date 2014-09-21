<?php

  use Angie\HTML;

  /**
   * Help element text helpers
   *
   * @package Shade
   */
  class FwHelpElementHelpers
  {
    /**
     * Current help element
     *
     * @var HelpBook|HelpBookPage|HelpVideo|HelpWhatsNewArticle
     */
    private static $current_element;

    /**
     * Return current element
     *
     * @return HelpElement
     */
    public static function getCurrentElement()
    {
      return self::$current_element;
    } // getCurrentElement

    /**
     * Set current element
     *
     * @param HelpElement $element
     */
    public static function setCurrentElement(HelpElement $element)
    {
      self::$current_element = $element;
    } // setCurrentElement

    /**
     * Image function
     *
     * @param  array  $params
     * @param  Smarty $smarty
     * @return string
     */
    public static function function_image($params, &$smarty)
    {
      return AngieApplication::help()->getImageUrl(static::getCurrentElement(), strtolower(array_required_var($params, 'name')));
    } // function_image

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
          $result .= '<li><a href="' . clean($video->getUrl()) . '">' . clean($video->getTitle()) . '</a> <span class="play_time" title="' . lang('Video Play Time') . '">(' . clean($video->getPlayTime()) . ')</span>';

          if ($video->getDescription()) {
            $result .= ' &mdash; ' . clean($video->getDescription());
          } // if

          $result .= '</li>';
        } // if
      } // foreach

      if ($result) {
        return '<div class="related_videos"><h3>' . lang('Related Video') . '</h3><ul>' . $result . '</ul></div>';
      } // if

      return '';
    } // function_related_video

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
      } // if

      $name = array_required_var($params, 'name', true);

      $article = AngieApplication::help()->getWhatsNew(\Angie\Authentication::getLoggedUser())->get($name);

      if ($article instanceof HelpWhatsNewArticle) {
        $params['href'] = $article->getUrl();

        return HTML::openTag('a', $params, $content);
      } else {
        return $content;
      } // if
    } // block_news_article

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
      } // if

      $name = array_required_var($params, 'name', true);

      $book = AngieApplication::help()->getBooks(\Angie\Authentication::getLoggedUser())->get($name);

      if ($book instanceof HelpBook) {
        $params['href'] = $book->getUrl();

        if ($book->getDescription()) {
          $params['title'] = $book->getDescription();
        } // if

        return HTML::openTag('a', $params, $content);
      } else {
        return $content;
      } // if
    } // block_book

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
      } // if

      $name = array_required_var($params, 'name', true);
      $book_name = array_var($params, 'book', null, true);

      if (empty($book_name)) {
        if (self::$current_element instanceof HelpBook) {
          $book_name = self::$current_element->getShortName();
        } elseif (self::$current_element instanceof HelpBookPage) {
          $book_name = self::$current_element->getBookName();
        } // if
      } // if

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
          } // if

          $params['data-page-name'] = $page->getShortName();
          $params['data-book-name'] = $book->getShortName();

          return HTML::openTag('a', $params, $content);
        } else {
          $development_error_message = 'Page not found';
        } // if
      } else {
        $development_error_message = 'Book not found';
      } // if

      if (AngieApplication::isInDevelopment() && isset($development_error_message)) {
        return '<span style="color: red; border-bottom: 1px dotted red; cursor: help;" title="Invalid page link: ' . clean($development_error_message) . '">' . clean($content) . '</span>';
      } else {
        return $content;
      } // if
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
      } // if

      $name = array_required_var($params, 'name', true);

      $video = AngieApplication::help()->getVideos(\Angie\Authentication::getLoggedUser())->get($name);

      if ($video instanceof HelpVideo) {
        $params['href'] = $video->getUrl();

        if ($video->getDescription()) {
          $params['title'] = $video->getDescription();
        } // if

        return HTML::openTag('a', $params, $content);
      } else {
        return $content;
      } // if
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
      } // if

      $class = array_var($params, 'class');

      if (empty($class)) {
        $params['class'] = 'note';
      } else {
        $params['class'] .= ' note';
      } // if

      $title = array_var($params, 'title', null, true);

      if ($title) {
        $params['class'] .= ' with_title';

        return HTML::openTag('div', $params, function () use ($title, $content) {
          return '<h3>' . clean($title) . '</h3>' . HTML::markdownToHtml(trim($content));
        });
      } else {
        return HTML::openTag('div', $params, function () use ($content) {
          return HTML::markdownToHtml(trim($content));
        });
      } // if
    } // block_note

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
      } // if

      if (empty($params['class'])) {
        $params['class'] = 'outlined_inline option';
      } else {
        $params['class'] .= ' outlined_inline option';
      } // if

      return HTML::openTag('span', $params, function () use ($content) {
        return clean(trim($content));
      });
    } // block_option

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
      } // if

      if (empty($params['class'])) {
        $params['class'] = 'outlined_inline term';
      } else {
        $params['class'] .= ' outlined_inline term';
      } // if

      return HTML::openTag('span', $params, function () use ($content) {
        return clean(trim($content));
      });
    } // block_term

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
      } // if

      if (empty($params['class'])) {
        $params['class'] = 'outlined_inline outlined_inline_mono path';
      } else {
        $params['class'] .= ' outlined_inline outlined_inline_mono path';
      } // if

      return HTML::openTag('span', $params, function () use ($content) {
        return clean(trim($content));
      });
    } // block_path

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
      } // if

      $content = trim($content); // Remove whitespace

      if (array_key_exists('inline', $params)) {
        $inline = (boolean) array_var($params, 'inline', false, true);
      } else {
        $inline = strpos($content, "\n") === false;
      } // if

      if ($inline) {
        if (empty($params['class'])) {
          $params['class'] = 'outlined_inline outlined_inline_mono inline_code';
        } else {
          $params['class'] .= ' outlined_inline outlined_inline_mono inline_code';
        }

        return HTML::openTag('span', $params, function () use ($content) {
          return clean(trim($content));
        });
      } else {
        $highlight = array_var($params, 'highlight', null, true);

        if (empty($highlight)) {
          $highlight = HyperlightForAngie::SYNTAX_PLAIN;
        } elseif ($highlight == 'php') {
          $highlight = HyperlightForAngie::SYNTAX_PHP;
        } // if

        return HyperlightForAngie::htmlPreview($content, $highlight);
      } // if
    } // block_code

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
      } // if

      $slug = array_var($params, 'slug', null, true);

      if (empty($slug)) {
        $slug = Angie\Inflector::slug($content);
      } // if

      return '<h3 id="s-' . clean($slug) . '" class="sub_header">' . clean($content) . ' <a href="#s-' . clean($slug) . '" title="' . lang('Link to this Section') . '" class="sub_permalink">#</a></h3>';
    } // block_sub

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
      } // if

      $num = (integer) array_var($params, 'num', null, true);

      if (empty($num)) {
        $num = 1;
      } // if

      return '<div class="step step-' . $num . '">
        <div class="step_num"><span>' . $num . '</span></div>
        <div class="step_content">' . HTML::markdownToHtml(trim($content)) . '</div>
      </div>';
    } // block_step

  }
