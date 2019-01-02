<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Linker;

use ActiveCollab\Shade\Ability\BuildableInterface;

class Linker implements LinkerInterface
{
    private $default_locale;

    public function __construct(string $default_locale = null)
    {
        $this->default_locale = $default_locale;
    }

    public function getUrl(string $path, BuildableInterface $relativeTo, string $locale = null): string
    {
        return $this->pageLevelToPrefix($relativeTo->getPageLevel(), $locale) . $path;
    }

    /**
     * @param  int         $page_level
     * @param  string|null $locale
     * @return string
     */
    public function pageLevelToPrefix(int $page_level, string $locale = null)
    {
        if ($locale && $locale != $this->default_locale) {
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
}
