<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Factory\SmartyFactory;

use ActiveCollab\Shade\Error\TempNotFoundError;
use ActiveCollab\Shade\Plugin\PluginInterface;
use ActiveCollab\Shade\Project\ProjectInterface;
use ActiveCollab\Shade\SmartyHelpers;
use ActiveCollab\Shade\ThemeInterface;
use ReflectionClass;
use ReflectionMethod;
use Smarty;

class SmartyFactory implements SmartyFactoryInterface
{
    public function createSmarty(ProjectInterface $project, ThemeInterface $theme, PluginInterface ...$plugins): Smarty
    {
        $smarty = new Smarty();

        $temp_path = $project->getTempPath();

        if (is_dir($temp_path)) {
            $smarty->setCompileDir($temp_path);
        } else {
            throw new TempNotFoundError($temp_path);
        }

        $smarty->setTemplateDir($theme->getPath() . '/templates');
        $smarty->compile_check = true;
        $smarty->left_delimiter = '<{';
        $smarty->right_delimiter = '}>';
        $smarty->registerFilter('variable', '\ActiveCollab\Shade\Shade::clean'); // {$foo nofilter}

        $helpers = new ReflectionClass(SmartyHelpers::class);

        foreach ($helpers->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
            $method_name = $method->getName();

            if (substr($method_name, 0, 6) === 'block_') {
                $smarty->registerPlugin(
                    'block',
                    substr($method_name, 6),
                    [
                        SmartyHelpers::class,
                        $method_name,
                    ]
                );
            } elseif (substr($method_name, 0, 9) === 'function_') {
                $smarty->registerPlugin(
                    'function',
                    substr($method_name, 9),
                    [
                        SmartyHelpers::class,
                        $method_name,
                    ]
                );
            };
        }

        SmartyHelpers::setDefaultLocale($project->getDefaultLocale());

        $smarty->assign(
            [
                'project' => $project,
                'default_locale' => $project->getDefaultLocale(),
                'copyright' => $project->getConfigurationOption('copyright', '--UNKNOWN--'),
                'copyright_since' => $project->getConfigurationOption('copyright_since'),
                'page_level' => 0,
                'plugins' => $plugins,
            ]
        );

        return $smarty;
    }
}
