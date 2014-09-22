<?php

  namespace Shade\Element;

  /**
   * Framework level help book implementation
   *
   * @package Shade
   */
  class Book extends Element
  {
    /**
     * Return book title
     *
     * @return string
     */
    public function getTitle()
    {
      return $this->getProperty('title', 'Book');
    }

    /**
     * Return property description
     *
     * @return string
     */
    public function getDescription()
    {
      return $this->getProperty('description');
    }

    /**
     * Return book cover URL
     *
     * @param  bool   $small
     * @return string
     */
    public function getCoverUrl($small = true)
    {
      $size_suffix = $small ? 'small' : 'large';
      $cover_image_file = "/_cover_$size_suffix.png";
      $image_relative_path = "books/" . str_replace('_', '-', $this->getFolderName()) . $cover_image_file;

      if (is_file($this->path . '/images' . $cover_image_file)) {
        return AngieApplication::getImageUrl($image_relative_path, $this->getModuleName(), 'help');
      } else {
        return AngieApplication::getImageUrl('book.png', HelpFramework::NAME);
      }
    }

    /**
     * Cached pages, per user
     *
     * @var array
     */
    private $pages = [];

    /**
     * Show pages that $user can see
     *
     * @param  User                     $user
     * @return HelpBookPage[]|Angie\NamedList
     */
    public function getPages(User $user = null)
    {
      $key = $user instanceof User ? $user->getId() : 'all';

      if (empty($this->pages[$key])) {
        $pages = new Angie\NamedList();

        $files = get_files($this->path . '/pages', 'md', false);

        if ($files && is_foreachable($files)) {
          sort($files); // Make sure that files are properly sorted

          foreach ($files as $file) {
            $page = new HelpBookPage($this->module, $this, $file, true);

            if ($page->isLoaded()) {
              $pages->add($page->getShortName(), $page);
            }
          }
        }

        $this->pages[$key] = $pages;
      }

      return $this->pages[$key];
    }

    /**
     * Populate list of common questions
     *
     * @param array $common_questions
     * @param User  $user
     */
    public function populateCommonQuestionsList(&$common_questions, User $user = null)
    {
      foreach ($this->getPages($user) as $page) {
        $answers_common_question = $page->getProperty('answers_common_question');

        if ($answers_common_question) {
          $common_questions[] = array(
            'question' => $answers_common_question,
            'page_url' => $page->getUrl(),
            'position' => (integer) $page->getProperty('answer_position'),
          );
        }
      }
    }

  }
