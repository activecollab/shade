<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\NamedList;

class Book extends Element
{
    public function getTitle(): string
    {
        return $this->getProperty('title', 'Book');
    }

    /**
     * Return property description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getProperty('description');
    }

    /**
     * Cached pages, per user.
     *
     * @var array
     */
    private $pages = false;

    /**
     * Show pages that $user can see.
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
     * Return a book page.
     *
     * @param  string   $name
     * @return BookPage
     */
    public function getPage($name)
    {
        return $this->getPages()->get($name);
    }

    /**
     * @var int
     */
    private $position = false;

    /**
     * Return book position.
     *
     * @return int
     */
    public function getPosition()
    {
        if ($this->position === false) {
            $this->position = (integer) $this->getProperty('position');
        }

        return $this->position;
    }

    /**
     * Populate list of common questions.
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
                    'book' => $this->getShortName(),
                    'page' => $page->getShortName(),
                    'position' => (integer) $page->getProperty('answer_position'),
                ];
            }
        }
    }

}
