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
use ActiveCollab\Shade\Transformator\Dom\DomTransformation;
use voku\helper\SimpleHtmlDom;

class InlineCodeTransformation extends DomTransformation implements CodeTransformationInterface
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
        $this->withClass($simpleHtmlDom, 'inline-code');
    }
}
