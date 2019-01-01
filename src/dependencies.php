<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

use ActiveCollab\Shade\Factory\ProjectFactory\ProjectFactory;
use ActiveCollab\Shade\Factory\ProjectFactory\ProjectFactoryInterface;
use ActiveCollab\Shade\Factory\SmartyFactory\SmartyFactory;
use ActiveCollab\Shade\Factory\SmartyFactory\SmartyFactoryInterface;
use ActiveCollab\Shade\Loader\Loader;
use ActiveCollab\Shade\Loader\LoaderInterface;
use ActiveCollab\Shade\MarkdownToHtml\MarkdownToHtml;
use ActiveCollab\Shade\MarkdownToHtml\MarkdownToHtmlInterface;
use ActiveCollab\Shade\Renderer\Renderer;
use ActiveCollab\Shade\Renderer\RendererInterface;
use function DI\get;

return [
    LoaderInterface::class => get(Loader::class),
    MarkdownToHtmlInterface::class => get(MarkdownToHtml::class),
    ProjectFactoryInterface::class => get(ProjectFactory::class),
    RendererInterface::class => get(Renderer::class),
    SmartyFactoryInterface::class => get(SmartyFactory::class),
];
