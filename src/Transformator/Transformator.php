<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator;

use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\Transformator\Dom\DomTransformationInterface;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDomNode;

class Transformator implements TransformatorInterface
{
    /**
     * @var DomTransformationInterface[]
     */
    private $domTransformators = [];

    public function transform(string $markdown_content): string
    {
        $html = Shade::markdownToHtml($markdown_content);

        if (!empty($this->domTransformators)) {
            $dom = HtmlDomParser::str_get_html($html);

            foreach ($this->domTransformators as $selector => $dom_transformator) {
                /** @var SimpleHtmlDomNode $elements */
                $elements = $dom->find($selector);

                if ($elements->count()) {
                    foreach ($elements as $element) {

                    }
                }
            }
        }

        return $html;
    }
}
