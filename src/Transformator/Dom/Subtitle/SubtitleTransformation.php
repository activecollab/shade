<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator\Dom\Subtitle;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\Transformator\Dom\DomTransformation;
use voku\helper\SimpleHtmlDom;

class SubtitleTransformation extends DomTransformation
{
    public function getSelector(): string
    {
        return 'h2';
    }

    public function transform(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        SimpleHtmlDom $simpleHtmlDom
    )
    {
        if (empty($simpleHtmlDom->getAttribute('id'))) {
            $slug = $simpleHtmlDom->getAttribute('data-slug');

            if (empty($slug)) {
                $slug = Shade::slug($simpleHtmlDom->innerHtml);
            }

            $clean_slug = Shade::clean('s-' . $slug);

            $simpleHtmlDom->outerHtml = sprintf(
                '<h2 id="%s" class="sub_header">%s <a href="#%s" title="%s" class="sub_permalink">#</a></h2>',
                $clean_slug,
                Shade::clean($simpleHtmlDom->innerHtml),
                $clean_slug,
                Shade::lang('Link to this Section')
            );
        }
    }
}
