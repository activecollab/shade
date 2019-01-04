<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Renderer;

use ActiveCollab\Shade\Ability\BuildableInterface;
use ActiveCollab\Shade\Ability\LoadableInterface;
use ActiveCollab\Shade\Element\ElementInterface;
use ActiveCollab\Shade\Factory\SmartyFactory\SmartyFactoryInterface;
use ActiveCollab\Shade\MarkdownToHtml\MarkdownToHtmlInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\Shade;
use ActiveCollab\Shade\SmartyHelpers;
use ActiveCollab\Shade\Transformator\TransformatorInterface;

class Renderer implements RendererInterface
{
    private $smartyFactory;
    private $markdownToHtml;
    private $propertiesSeparator;
    /**
     * @var TransformatorInterface
     */
    private $transformator;

    public function __construct(
        SmartyFactoryInterface $smartyFactory,
        MarkdownToHtmlInterface $markdownToHtml,
        TransformatorInterface $transformator,
        string $propertiesSeparator = LoadableInterface::PROPERTIES_SEPARATOR
    )
    {
        $this->smartyFactory = $smartyFactory;
        $this->propertiesSeparator = $propertiesSeparator;
        $this->transformator = $transformator;
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

        return $this->transformator->transform($project, $buildableElement, $content);
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
