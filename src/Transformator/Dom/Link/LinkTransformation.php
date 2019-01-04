<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator\Dom\Link;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Ability\DescribableInterface;
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
            $target = $resolution->getTarget();

            $params = [
                'href' => $target->getUrl($buildableElement) . $this->resolveSection($simpleHtmlDom),
                'class' => $this->resolveLinkClass($buildableElement),
            ];

            if ($target instanceof DescribableInterface) {
                $params['title'] = $target->getDescription();
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
            case 'book':
                return $this->resolveBook($project, $buildableElement, $simpleHtmlDom);
            case 'page':
                return $this->resolvePage($project, $buildableElement, $simpleHtmlDom);
            default:
                return new LinkResolution(
                    null,
                    sprintf('Finder not implemented for target "%s"', $target)
                );
        }
    }

    private function resolveBook(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        SimpleHtmlDom $simpleHtmlDom
    ): LinkResolutionInterface
    {
        $book_name = $simpleHtmlDom->getAttribute('data-book-name');

        if (empty($book_name)) {
            if ($buildableElement instanceof Book) {
                return new LinkResolution($buildableElement);
            } elseif ($buildableElement instanceof BookPage) {
                $book = $project->getBook($buildableElement->getBookName());

                if ($book instanceof Book) {
                    return new LinkResolution($book);
                }
            }

            throw new ParamRequiredError('data-book-name');
        } else {
            $book = $project->getBook($book_name);

            if ($book instanceof Book) {
                return new LinkResolution($book);
            } else {
                return new LinkResolution(
                    null,
                    sprintf('Book "%s" not found', $book_name)
                );
            }
        }
    }

    private function resolvePage(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        SimpleHtmlDom $simpleHtmlDom
    ): LinkResolutionInterface
    {
        $page_name = $simpleHtmlDom->getAttribute('data-page-name');

        if (empty($page_name)) {
            throw new ParamRequiredError('data-page-name');
        }

        $book_name = $simpleHtmlDom->getAttribute('data-book-name');

        if (empty($book_name)) {
            if ($buildableElement instanceof Book) {
                $book_name = $buildableElement->getShortName();
            } elseif ($buildableElement instanceof BookPage) {
                $book_name = $buildableElement->getBookName();
            }
        }

        $book = $book_name ? $project->getBook($book_name) : null;

        if ($book instanceof Book) {
            $page = $book->getPage($page_name);

            if ($page instanceof BookPage) {
                return new LinkResolution($page);
            } else {
                return new LinkResolution(
                    null,
                    sprintf('Page "%s" not found in "%s" book', $page_name, $book->getTitle())
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

    private function resolveLinkClass(BuildableInterface $buildableElement): ?string
    {
        if ($buildableElement instanceof Book) {
            return 'link_to_book';
        } elseif ($buildableElement instanceof BookPage) {
            return 'link_to_book_page';
        }

        return null;
    }
}
