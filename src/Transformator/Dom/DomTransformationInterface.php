<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator\Dom;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use voku\helper\SimpleHtmlDom;

interface DomTransformationInterface
{
    public function getSelector(): string;
    public function transform(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        SimpleHtmlDom $simpleHtmlDom
    );
}
