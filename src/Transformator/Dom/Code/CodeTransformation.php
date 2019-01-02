<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator\Dom\Code;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\Transformator\Dom\DomTransformation;
use voku\helper\SimpleHtmlDom;

class CodeTransformation extends DomTransformation implements CodeTransformationInterface
{
    public function getSelector(): string
    {
        return 'code';
    }

    public function transform(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        SimpleHtmlDom $simpleHtmlDom
    )
    {
        $is_inline = $this->resolveIsInline($simpleHtmlDom);

        if (!$is_inline) {
            $simpleHtmlDom->outerText = sprintf(
                '<pre>%s</pre>',
                Shade::clean($simpleHtmlDom->innerText)
            );
        }
    }

    private function resolveIsInline(SimpleHtmlDom $simpleHtmlDom): bool
    {
        if ($simpleHtmlDom->hasAttribute('data-display')) {
            return $simpleHtmlDom->getAttribute('data-display') === 'inline';
        } else {
            return strpos($simpleHtmlDom->innerHtml(), "\n") === false;
        }
    }
}
