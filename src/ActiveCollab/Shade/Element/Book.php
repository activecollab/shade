<?php

  namespace ActiveCollab\Shade\Element;

  use ActiveCollab\Shade\NamedList;

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
    private $pages = false;

    /**
     * Show pages that $user can see
     *
     * @return BookPage[]|NamedList
     */
    public function getPages()
    {
      if (empty($this->pages)) {
        $this->pages = $this->getProject()->getBookPages($this);
      }

      return $this->pages;
    }

    /**
     * Return a book page
     *
     * @param string $name
     * @return BookPage
     */
    public function getPage($name)
    {
      return $this->getPages()->get($name);
    }

    /**
     * Populate list of common questions
     *
     * @param array $common_questions
     */
    public function populateCommonQuestionsList(array &$common_questions)
    {
      foreach ($this->getPages() as $page) {
        $answers_common_question = $page->getProperty('answers_common_question');

        if ($answers_common_question) {
          $common_questions[] = [
            'question' => $answers_common_question,
            'page_url' => $page->getUrl(),
            'position' => (integer) $page->getProperty('answer_position'),
          ];
        }
      }
    }

  }
