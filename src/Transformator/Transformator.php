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
    private $domTransformations = [];

    public function __construct(
        MarkdownToHtmlInterface $markdownToHtml,
        DomTransformationInterface ...$domTransformations
    )
    {
        $this->markdownToHtml = $markdownToHtml;

        $this->addDomTransformations(...$domTransformations);
    }

    public function addDomTransformations(DomTransformationInterface ...$domTransformations)
    {
        $this->domTransformations = array_merge(
            $this->domTransformations,
            $domTransformations
        );
    }

    public function transform(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        string $markdownContent
    ): string
    {
        $html = $this->markdownToHtml->markdownToHtml($markdownContent);

        if (!empty($this->domTransformations)) {
            foreach ($this->domTransformations as $dom_transformation) {
                $dom = HtmlDomParser::str_get_html($html);

                /** @var SimpleHtmlDomNode $elements */
                $elements = $dom->find($dom_transformation->getSelector());

                if ($elements->count()) {
                    foreach ($elements as $element) {
                        $dom_transformation->transform($project, $buildableElement, $element);
                    }
                }

                $html = $dom->html();
            }
        }

        return $html;
    }
}
