<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Test;

use ActiveCollab\Shade\Transformator\Dom\Code\CodeTransformation;
use PHPUnit\Framework\TestCase;
use voku\helper\HtmlDomParser;

class CodeTransformationTest extends TestCase
{
    public function testWillTransformOnlyFirstLevel()
    {
        $html = '<p>Here we have some code:</p><pre data-level="first"><pre data-level="second">Code within code</pre></pre>';

        $dom = HtmlDomParser::str_get_html($html);

        $nodes = $dom->find((new CodeTransformation())->getSelector());

        $this->assertCount(1, $nodes);

        $this->assertSame('first', $nodes[0]->getAttribute('data-level'));
    }
}
