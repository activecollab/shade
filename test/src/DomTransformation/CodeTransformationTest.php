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
use ActiveCollab\Shade\Transformator\Dom\Code\CodeBlockTransformation;
use ActiveCollab\Shade\Transformator\Dom\Code\InlineCodeTransformation;
use ActiveCollab\Shade\Transformator\Transformator;
use PHPUnit\Framework\TestCase;
use voku\helper\HtmlDomParser;

class CodeTransformationTest extends TestCase
{
    public function testWillTransformOnlyFirstLevel()
    {
        $html = '<p>Here we have some code:</p><pre data-level="first"><pre data-level="second">Code within code</pre></pre>';

        $dom = HtmlDomParser::str_get_html($html);

        $nodes = $dom->find((new CodeBlockTransformation())->getSelector());

        $this->assertCount(1, $nodes);

        $this->assertSame('first', $nodes[0]->getAttribute('data-level'));
    }

    public function testInlineCodeWillEscapeHtml()
    {
        $inline_code_transformation = new InlineCodeTransformation();

        $html = 'Here is some <code>&lt;pre&gt;</code> example';

        $dom = HtmlDomParser::str_get_html($html);

        $nodes = $dom->find($inline_code_transformation->getSelector());

        $this->assertCount(1, $nodes);

        /** @var ProjectInterface $project */
        $project = $this->createMock(ProjectInterface::class);

        /** @var BuildableInterface $buildable_element */
        $buildable_element = $this->createMock(BuildableInterface::class);

        $inline_code_transformation->transform($project, $buildable_element, $nodes[0]);

        $this->assertSame(
            'Here is some <code class="inline-code">&lt;pre&gt;</code> example',
            $dom->html()
        );
    }

    public function testWillTransformSubtitlesFromMarkdown()
    {
        $markdown = file_get_contents(dirname(__DIR__, 2) . '/fixtures/inline-and-block-code.md');

        $transformator = new Transformator(
            new MarkdownToHtml(),
            new CodeBlockTransformation(),
            new InlineCodeTransformation()
        );

        /** @var ProjectInterface $project */
        $project = $this->createMock(ProjectInterface::class);

        /** @var BuildableInterface $buildable_element */
        $buildable_element = $this->createMock(BuildableInterface::class);

        $html = $transformator->transform($project, $buildable_element, $markdown);

        $this->assertSame(1, substr_count($html, '<pre>'));
        $this->assertSame(1, substr_count($html, '</pre>'));

        $this->assertSame(1, substr_count($html, '<code class="first inline-code">'));
        $this->assertSame(1, substr_count($html, '<code class="second inline-code">'));
        $this->assertSame(2, substr_count($html, '</code>'));
    }
}
