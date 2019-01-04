<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Element;

use ActiveCollab\Shade\Ability\DescribableInterface;

class Video extends Element implements DescribableInterface
{
    const GETTING_STARTED = 'getting-started';

    public function getShortName(): string
    {
        return $this->getSlug();
    }

    /**
     * Cached group name value.
     *
     * @var string
     */
    private $group_name = false;

    /**
     * Return name of the group that this video belongs to.
     *
     * @return string
     */
    public function getGroupName()
    {
        if ($this->group_name === false) {
            $this->group_name = $this->getProperty('group');

            if (empty($this->group_name)) {
                $this->group_name = self::GETTING_STARTED;
            }
        }

        return $this->group_name;
    }

    public function getDescription(): string
    {
        return $this->getProperty('description', '');
    }

    /**
     * Return source URL.
     *
     * Only supported modifier at the moment is 2X
     *
     * @param  string $modifier
     * @return string
     */
    public function getSourceUrl($modifier = null)
    {
        if (empty($modifier)) {
            return $this->getProperty('url');
        } else {
            return $this->getProperty('url' . strtolower($modifier));
        }
    }

    /**
     * Cached play time value.
     *
     * @var string
     */
    private $play_time = false;

    /**
     * Return video play time.
     *
     * @return string
     */
    public function getPlayTime()
    {
        if ($this->play_time === false) {
            $this->play_time = $this->getProperty('play_time');

            if (empty($this->play_time)) {
                $this->play_time = '-:--';
            }
        }

        return $this->play_time;
    }
}
