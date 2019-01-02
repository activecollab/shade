<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\MarkdownToHtml\MarkdownToHtmlInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Transformator\Dom\DomTransformationInterface;
use voku\helper\HtmlDomParser;
use voku\helper\SimpleHtmlDomNode;

class Transformator implements TransformatorInterface
{
    private $markdownToHtml;

    /**
     * @var DomTransformationInterface[]
     */
    private $domTransformators = [];

    public function __construct(MarkdownToHtmlInterface $markdownToHtml)
    {
        $this->markdownToHtml = $markdownToHtml;
    }

    public function transform(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        string $markdownContent
    ): string
    {
        $html = $this->markdownToHtml->markdownToHtml($markdownContent);

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
