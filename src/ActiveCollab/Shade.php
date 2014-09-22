<?php

  /**
   * Main class for interaction with Shade projects
   */
  final class Shade
  {
    /**
     * Name of the default video group
     */
    const GETTING_STARTED_VIDEO_GROUP = 'getting-started';

    /**
     * Cached array of articles
     *
     * @var array
     */
    private $whats_new_articles = [];

    /**
     * Return list of articles that $user can see
     *
     * @param  User|null                       $user
     * @return HelpWhatsNewArticle[]|NamedList
     */
    public function getWhatsNew(User $user = null)
    {
      $key = $user instanceof User ? $user->getId() : 'all';

      if (empty($this->whats_new_articles[$key])) {
        $articles = new NamedList();

        foreach (AngieApplication::getModules() as $module) {
          $whats_new_folders = get_folders($module->getPath() . '/help/whats_new');

          if ($whats_new_folders) {
            rsort($whats_new_folders);

            foreach ($whats_new_folders as $whats_new_folder) {
              $version_number = basename($whats_new_folder);

              if ($this->isValidVersionNumber($version_number)) {
                $whats_new_files = get_files($whats_new_folder, 'md');

                if ($whats_new_files) {
                  sort($whats_new_files);

                  foreach ($whats_new_files as $whats_new_file) {
                    $article = new HelpWhatsNewArticle($module->getName(), $version_number, $whats_new_file);

                    if ($article->isLoaded()) {
                      if ($user instanceof User && !$article->canView($user)) {
                        continue;
                      } // if

                      $articles->add($article->getShortName(), $article);
                    } // if
                  } // foreach
                } // if
              } // if
            } // foreach
          } // if
        } // foreach

        $this->whats_new_articles[$key] = $articles;
      } // if

      return $this->whats_new_articles[$key];
    } // getWhatsNew

    /**
     * Cached previous version number
     *
     * @var bool
     */
    private $previous_version = false;

    /**
     * Returns true if this article is new since last upgrade
     *
     * @param  HelpWhatsNewArticle $article
     * @return bool
     */
    public function isNewSinceLastUpgrade(HelpWhatsNewArticle $article)
    {
      if ($this->previous_version === false) {
        $update_history_table = TABLE_PREFIX . 'update_history';

        if (DB::executeFirstCell("SELECT COUNT(id) FROM $update_history_table") > 1) {
          $this->previous_version = DB::executeFirstCell("SELECT version FROM $update_history_table ORDER BY created_on DESC LIMIT 1, 1");
        } else {
          $this->previous_version = null;
        } // if
      } // if

      if ($this->previous_version) {
        return version_compare($article->getVersionNumber(), $this->previous_version) >= 0;
      } else {
        return true; // Everything is new when you install a fresh copy of the system
      } // if
    } // isNewSinceLastUpgrade

    /**
     * Returns true if $version is a valid angie application version number
     *
     * @param  string  $version
     * @return boolean
     */
    private function isValidVersionNumber($version)
    {
      if (strpos($version, '.') !== false) {
        $parts = explode('.', $version);

        if (count($parts) == 3) {
          foreach ($parts as $part) {
            if (!is_numeric($part)) {
              return false;
            } // if
          } // foreach

          return true;
        } else {
          return false;
        } // if
      } else {
        return false;
      } // if
    } // isValidVersionNumber

    /**
     * Cached list of books
     *
     * @var array
     */
    private $books = array();

    /**
     * Return books that $user can access
     *
     * @param  User                 $user
     * @return HelpBook[]|NamedList
     */
    public function getBooks(User $user = null)
    {
      $key = $user instanceof User ? $user->getId() : 'all';

      if (empty($this->books[$key])) {
        $books = new NamedList();

        $possible_locations = array();

        foreach (AngieApplication::getFrameworks() as $framework) {
          $possible_locations[$framework->getName()] = $framework->getPath() . '/help/books';
        } // foreach

        foreach (AngieApplication::getModules() as $module) {
          $possible_locations[$module->getName()] = $module->getPath() . '/help/books';
        } // foreach

        foreach ($possible_locations as $module_name => $path) {
          $book_folders = get_folders($path);

          if ($book_folders) {
            foreach ($book_folders as $book_folder) {
              $book = new HelpBook($module_name, $book_folder);

              if ($book->isLoaded()) {
                if ($user instanceof User && !$book->canView($user)) {
                  continue; // Skip if user can't see this book
                } // if

                $books->add($book->getShortName(), $book);
              } // if
            } // foreach
          } // if
        } // foreach

        $books->sort(function ($a, $b) {
          if ($a instanceof HelpBook && $b instanceof HelpBook) {
            $order_a = (integer) $a->getProperty('position');
            $order_b = (integer) $b->getProperty('position');

            if ($order_a == $order_b) {
              return 0;
            } // if

            return ($order_a < $order_b) ? -1 : 1;
          } // if

          return 0;
        });

        $this->books[$key] = $books;
      } // if

      return $this->books[$key];
    } // getBooks

    /**
     * Return array of common questions
     */
    public function getCommonQuestions(User $user = null)
    {
      if ($user instanceof User) {
        $cache_key = $user->getCacheKey('common_help_questions');
      } else {
        $cache_key = 'common_help_questions';
      } // if

      return AngieApplication::cache()->get($cache_key, function () use ($user) {
        $result = array();

        foreach (AngieApplication::help()->getBooks($user) as $book) {
          $book->populateCommonQuestionsList($result, $user);
        } // foreach

        usort($result, function ($a, $b) {
          if ($a['position'] == $b['position']) {
            return 0;
          } // if

          return ($a['position'] < $b['position']) ? -1 : 1;
        });

        return $result;
      });
    } // getCommonQuestions

    /**
     * Return array of video groups
     *
     * @return NamedList
     */
    public function getVideoGroups()
    {
      return new NamedList(array(
      AngieHelpDelegate::GETTING_STARTED_VIDEO_GROUP => lang('Getting Started'),
      ));
    } // getVideoGroups

    /**
     * Cached array of videos
     *
     * @var array
     */
    private $videos = array();

    /**
     * Return videos that $user can access
     *
     * @param  User                  $user
     * @return HelpVideo[]|NamedList
     */
    public function getVideos(User $user = null)
    {
      $key = $user instanceof User ? $user->getId() : 'all';

      if (empty($this->videos[$key])) {
        $videos = new NamedList();

        foreach (AngieApplication::getModules() as $module) {
          $video_files = get_files($module->getPath() . '/help/videos', 'md');

          if ($video_files) {
            foreach ($video_files as $video_file) {
              $video = new HelpVideo($module->getName(), $video_file);

              if ($video->isLoaded()) {
                if ($user instanceof User && !$video->canView($user)) {
                  continue;
                } // if

                $videos->add($video->getShortName(), $video);
              } // if
            } // foreach
          } // if
        } // foreach

        $this->videos[$key] = $videos;
      } // if

      return $this->videos[$key];
    } // getVideos

    /**
     * Return user groups that describe this user
     *
     * @param  User  $user
     * @return array
     */
    public function getUserGroups(User $user)
    {
      $groups = array(get_class($user));

      if (AngieApplication::isInDevelopment()) {
        $groups[] = 'Developer';
      } // if

      if (AngieApplication::isOnDemand()) {
        $groups[] = 'On Demand User';
      } // if

//      if ($this->user_groups_callback instanceof Closure) {
//        $this->user_groups_callback->__invoke($user, $groups);
//      } // if
      return $groups;
    } // getUserGroups

    /**
     * Closure that is called when user groups is called
     *
     * @var Closure|null
     */
    private $user_groups_callback;

    /**
     * Set user groups callback
     *
     * @param $callback
     * @throws InvalidInstanceError
     */
    public function setOnUserGroupsCallback($callback)
    {
      if ($callback instanceof Closure || $callback === null) {
        $this->user_groups_callback = $callback;
      } else {
        throw new InvalidInstanceError('callback', $callback, 'Closure');
      } // if
    } // setOnUserGroupsCallback

    /**
     * Smarty instance that we will use to render content
     *
     * @var Smarty
     */
    private $smarty;

    /**
     * Render body of a given element
     *
     * @param  HelpElement $element
     * @param  User|null   $user
     * @return string
     */
    public function renderBody(HelpElement $element, User $user = null)
    {
      if (empty($this->smarty)) {
        $this->smarty = new Smarty();

        $this->smarty->setCompileDir(COMPILE_PATH);
        $this->smarty->setCacheDir(CACHE_PATH);
        $this->smarty->left_delimiter = '<{';
        $this->smarty->right_delimiter = '}>';
        $this->smarty->registerFilter('variable', 'clean'); // {$foo nofilter}

        $helper_class = new ReflectionClass('HelpElementHelpers');

        foreach ($helper_class->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC) as $method) {
          $method_name = $method->getName();

          if (str_starts_with($method_name, 'block_')) {
            $this->smarty->registerPlugin('block', substr($method_name, 6), array('HelpElementHelpers', $method_name));
          } elseif (str_starts_with($method_name, 'function_')) {
            $this->smarty->registerPlugin('function', substr($method_name, 9), array('HelpElementHelpers', $method_name));
          } // if;
        } // foreach

        require_once ANGIE_PATH . '/vendor/markdown/init.php';
      } // if

      $template = $this->smarty->createTemplate($element->getIndexFilePath());
      $template->assign('user', $user);

      HelpElementHelpers::setCurrentElement($element);

      $content = $template->fetch();

      HelpElementHelpers::setCurrentElement(null);

      $separator_pos = strpos($content, HelpElement::PROPERTIES_SEPARATOR);

      if ($separator_pos === false) {
        if (substr($content, 0, 1) == '*') {
          $content = '*Content Not Provided*';
        } // if
      } else {
        $content = trim(substr($content, $separator_pos + strlen(HelpElement::PROPERTIES_SEPARATOR)));
      } // if

      return HTML::markdownToHtml($content);
    } // renderBody

    // ---------------------------------------------------
    //  URL
    // ---------------------------------------------------

    /**
     * URL generator
     *
     * @var Closure|null
     */
    private $url_generator = null;

    /**
     * Set URL generator
     *
     * @param  Closure|null      $generator
     * @throws InvalidParamError
     */
    public function setUrlGenerator($generator)
    {
      if ($generator instanceof Closure || $generator === null) {
        $this->url_generator = $generator;
      } else {
        throw new InvalidParamError('generator', $generator, 'Closure');
      } // if
    } // setUrlGenerator

    /**
     * Return URL of an element
     *
     * @param  HelpElement       $element
     * @return string
     * @throws InvalidParamError
     */
    public function getUrl(HelpElement $element)
    {
      if ($this->url_generator && $this->url_generator instanceof Closure) {
        return $this->url_generator->__invoke($element);
      } else {
        if ($element instanceof HelpBook) {
          return Router::assemble('help_book', array(
          'book_name' => $element->getShortName()
          ));
        } elseif ($element instanceof HelpBookPage) {
          return Router::assemble('help_book_page', array(
          'book_name' => $element->getBookName(),
          'page_name' => $element->getSlug(),
          ));
        } elseif ($element instanceof HelpWhatsNewArticle) {
          return Router::assemble('help_whats_new_article', array(
          'article_name' => $element->getSlug(),
          ));
        } elseif ($element instanceof HelpVideo) {
          return Router::assemble('help_video', array(
          'video_name' => $element->getSlug()
          ));
        } else {
          throw new InvalidParamError('element', $element, 'HelpElement');
        } // if
      } // if
    } // getUrl

    /**
     * Image URL generator
     *
     * @var Closure|null
     */
    private $image_url_generator = null;

    /**
     * Set URL generator
     *
     * @param  Closure|null      $generator
     * @throws InvalidParamError
     */
    public function setImageUrlGenerator($generator)
    {
      if ($generator instanceof Closure || $generator === null) {
        $this->image_url_generator = $generator;
      } else {
        throw new InvalidParamError('generator', $generator, 'Closure');
      } // if
    } // setImageUrlGenerator

    /**
     * Return image URL
     *
     * @param  HelpElement $current_element
     * @param  string      $name
     * @return string
     */
    public function getImageUrl($current_element, $name)
    {
      if ($this->image_url_generator && $this->image_url_generator instanceof Closure) {
        return $this->image_url_generator->__invoke($current_element, $name);
      } else {
        if ($current_element instanceof HelpBookPage) {
          $params['src'] = AngieApplication::getImageUrl('books/' . $current_element->getBookName() . '/' . $name, $current_element->getModuleName(), 'help');
        } elseif ($current_element instanceof HelpBook) {
          $params['src'] = AngieApplication::getImageUrl('books/' . $current_element->getShortName() . '/' . $name, $current_element->getModuleName(), 'help');
        } elseif ($current_element instanceof HelpVideo) {
          $params['src'] = AngieApplication::getImageUrl('videos/' . $name, $current_element->getModuleName(), 'help');
        } elseif ($current_element instanceof HelpWhatsNewArticle) {
          $params['src'] = AngieApplication::getImageUrl('whats-new/' . $current_element->getVersionNumber() . '/' . $name, $current_element->getModuleName(), 'help');
        } else {
          return 'Unknown';
        } // if

        return '<div class="center">' . HTML::openTag('img', $params) . '</div>';
      } // if
    } // getImageUrl
  }