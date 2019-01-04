<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Test\DomTransformation;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\MarkdownToHtml\MarkdownToHtml;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Transformator\Dom\Subtitle\SubtitleTransformation;
use ActiveCollab\Shade\Transformator\Transformator;
use PHPUnit\Framework\TestCase;
use voku\helper\HtmlDomParser;

class SubtitleTransformationTest extends TestCase
{
    public function testWillTransformOnlyFirstLevel()
    {
        $html = '<p>Some text</p><h2 data-level="first">Subtitle</h2><code><h2 data-level="second">This is an example</h2></code>';

        $dom = HtmlDomParser::str_get_html($html);

        $nodes = $dom->find((new SubtitleTransformation())->getSelector());

        $this->assertCount(1, $nodes);

        $this->assertSame('first', $nodes[0]->getAttribute('data-level'));
    }

    public function testWillTransformCodeFromMarkdown()
    {
        $markdown = file_get_contents(dirname(__DIR__, 2) . '/fixtures/multi-level-h2-issue.md');

        $transformator = new Transformator(new MarkdownToHtml());

        /** @var ProjectInterface $project */
        $project = $this->createMock(ProjectInterface::class);

        /** @var BuildableInterface $buildable_element */
        $buildable_element = $this->createMock(BuildableInterface::class);

        $html = $transformator->transform($project, $buildable_element, $markdown);
        $this->assertSame(1, substr_count($html, '<h2 id="s-links" class="subtitle">'));
        $this->assertSame(1, substr_count($html, '</h2>'));
    }
}
