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
use ActiveCollab\Shade\Factory\SmartyFactory\SmartyFactory;
use ActiveCollab\Shade\MarkdownToHtml\MarkdownToHtmlInterface;
use ActiveCollab\Shade\ProjectInterface;
use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\SmartyHelpers;

class Renderer implements RendererInterface
{
    private $smartyFactory;
    private $markdownToHtml;
    private $propertiesSeparator;

    public function __construct(
        SmartyFactory $smartyFactory,
        MarkdownToHtmlInterface $markdownToHtml,
        string $propertiesSeparator = '================================================================'
    )
    {
        $this->smartyFactory = $smartyFactory;
        $this->propertiesSeparator = $propertiesSeparator;
        $this->markdownToHtml = $markdownToHtml;
    }

    public function renderProjectBody(ProjectInterface $project)
    {
        return $this->renderBody($project, $project);
    }

    public function renderElementBody(ElementInterface $element)
    {
        return $this->renderBody($element->getProject(), $element);
    }

    private function renderBody(ProjectInterface $project, BuildableInterface $buildableElement)
    {
        $content = $this->renderBuildableElementIndex($project, $buildableElement);

        $separator_pos = strpos($content, $this->propertiesSeparator);

        if ($separator_pos === false) {
            if (substr($content, 0, 1) == '*') {
                $content = '*Content Not Provided*';
            }
        } else {
            $content = trim(substr($content, $separator_pos + strlen($this->propertiesSeparator)));
        }

        return $this->markdownToHtml->markdownToHtml($content);
    }

    private function renderBuildableElementIndex(
        ProjectInterface $project,
        BuildableInterface $buildableElement
    ): string
    {
        $smarty = $this->smartyFactory->createSmarty(
            $project,
            $project->getBuildTheme(),
            ...Shade::getPlugins($project)
        );

        return $this->withCurrentElement(
            $project,
            $buildableElement,
            function (BuildableInterface $buildableElement) use ($smarty) {
                return $smarty->createTemplate($buildableElement->getIndexFilePath())->fetch();
            }
        );
    }

    private function withCurrentElement(
        ProjectInterface $project,
        BuildableInterface $buildableElement,
        callable $do
    ): string
    {
        SmartyHelpers::setCurrentProject($project);
        SmartyHelpers::setCurrentElement($buildableElement);

        $content = (string) call_user_func($do, $buildableElement);

        SmartyHelpers::resetCurrentElementAndProject();

        return $content;
    }
}
