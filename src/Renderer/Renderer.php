<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Renderer;

use ActiveCollab\Shade\BuildableInterface;
use ActiveCollab\Shade\Element\ElementInterface;
use ActiveCollab\Shade\ProjectInterface;
use ActiveCollab\Shade\SmartyHelpers;

class Renderer
{
    public function renderProjectBody(ProjectInterface $project)
    {

    }

    public function renderElementBody(ElementInterface $element)
    {

    }

    private function setCurrentElement(BuildableInterface $current_element)
    {
        SmartyHelpers::setCurrentElement($current_element);
    }

    private function unsetCurrentElement()
    {
        SmartyHelpers::resetCurrentElementAndProject();
    }
}
