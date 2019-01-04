<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator\Dom;

use voku\helper\SimpleHtmlDom;

abstract class DomTransformation implements DomTransformationInterface
{
    public function withClass(SimpleHtmlDom $simpleHtmlDom, string $className)
    {
        if ($simpleHtmlDom->hasAttribute('class')) {
            $class_attribute = $simpleHtmlDom->getAttribute('class');

            if (!in_array($className, explode(' ', $class_attribute))) {
                $simpleHtmlDom->setAttribute(
                    'class',
                    trim($class_attribute . ' ' . $className)
                );
            }
        } else {
            $simpleHtmlDom->setAttribute('class', $className);
        }
    }
}
