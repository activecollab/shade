<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator\Dom\Link;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Element\Book;
use ActiveCollab\Shade\Element\BookPage;
use ActiveCollab\Shade\Error\ParamRequiredError;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\Transformator\Dom\DomTransformation;
use ActiveCollab\Shade\Transformator\Dom\Link\Resolution\LinkResolution;
use ActiveCollab\Shade\Transformator\Dom\Link\Resolution\LinkResolutionInterface;
use voku\helper\SimpleHtmlDom;

class LinkTransformation extends DomTransformation implements LinkTransformationInterface
{
    public function getSelector(): string
    {
        return 'a[data-target]';
    }

    public function transform(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        SimpleHtmlDom $simpleHtmlDom
    )
    {
        $resolution = $this->resolveTarget($project, $buildableElement, $simpleHtmlDom);

        if ($resolution->isFound()) {
            $params['href'] = $resolution->getTarget()->getUrl($buildableElement) . $this->resolveSection($simpleHtmlDom);

            if (empty($params['class'])) {
                $params['class'] = 'link_to_book_page';
            } else {
                $params['class'] .= ' link_to_book_page';
            }

            $simpleHtmlDom->outerHtml = Shade::htmlTag('a', $params, $simpleHtmlDom->innerHtml);
        } else {
            $simpleHtmlDom->outerHtml = sprintf(
                '<a style="%s" href="#" title="%s">%s</a>',
                'color: red; border-bottom: 1px dotted red; cursor: help;',
                Shade::clean($resolution->getNotFoundMessage()),
                $simpleHtmlDom->innerHtml
            );
        }
    }

    private function resolveTarget(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        SimpleHtmlDom $simpleHtmlDom
    ): LinkResolutionInterface
    {
        $target = $simpleHtmlDom->getAttribute('data-target');

        switch ($target) {
            case 'page':
                return $this->resolvePage($project, $buildableElement, $simpleHtmlDom);
            default:
                return new LinkResolution(
                    null,
                    sprintf('Finder not implemented for target "%s"', $target)
                );
        }
    }

    private function resolvePage(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        SimpleHtmlDom $simpleHtmlDom
    ): LinkResolutionInterface
    {
        $name = $simpleHtmlDom->getAttribute('data-page-name');

        if (empty($name)) {
            throw new ParamRequiredError('data-page-name');
        }

        $book_name = $simpleHtmlDom->getAttribute('data-book-name');
        $section = $simpleHtmlDom->getAttribute('data-section');

        if (!empty($section)) {
            $section = '#s-' . Shade::slug($section);
        }

        if (empty($book_name)) {
            if ($buildableElement instanceof Book) {
                $book_name = $buildableElement->getShortName();
            } elseif ($buildableElement instanceof BookPage) {
                $book_name = $buildableElement->getBookName();
            }
        }

        $book = $book_name ? $project->getBook($book_name) : null;

        if ($book instanceof Book) {
            $page = $book->getPage($name);

            if ($page instanceof BookPage) {
                return new LinkResolution($page);
            } else {
                return new LinkResolution(
                    null,
                    sprintf('Page "%s" not found in "%s" book', $name, $book->getTitle())
                );
            }
        } else {
            return new LinkResolution(null, 'Book not found');
        }
    }

    private function resolveSection(SimpleHtmlDom $simpleHtmlDom): ?string
    {
        $section = $simpleHtmlDom->getAttribute('data-section');

        if (!empty($section)) {
            $section = '#s-' . Shade::slug($section);
        }

        return $section;
    }
}
