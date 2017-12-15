<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\Shade\Command;

use ActiveCollab\Shade\Element\Video;
use ActiveCollab\Shade\Project;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create a new project.
 *
 * @package ActiveCollab\Shade\Command
 */
class ProjectCommand extends Command
{
    function configure()
    {
        parent::configure();

        $this->setName('project')
            ->addArgument('name')
            ->addOption(
                'default-locale',
                null,
                InputArgument::OPTIONAL,
                'Turn on multilingual support and set the default locale'
            )
            ->setDescription('Create a new project');
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = new Project(getcwd());

        if ($project->isValid()) {
            $output->writeln('Project already initialized');
        } else {
            $configuration = [
                'name' => $input->getArgument('name'),
                'default_build_theme' => 'bootstrap',
                'video_groups' => [
                    Video::GETTING_STARTED => 'Getting Started',
                ],
            ];

            if (empty($configuration['name'])) {
                $configuration['name'] = basename($project->getPath());
            }

            $default_locale = $input->getOption('default-locale');

            if ($default_locale) {
                $configuration['is_multilingual'] = true;
                $configuration['default_locale'] = $default_locale;
            }

            if (file_put_contents($project->getPath() . '/project.json', json_encode($configuration, JSON_PRETTY_PRINT))) {
                $output->writeln('Project initialized');
            } else {
                $output->writeln('<error>Failed to create a project configuration file</error>');
            }

            if ($default_locale) {
                if (!mkdir($project->getPath() . '/' . $default_locale)) {
                    $output->writeln("<error>Failed to create '$default_locale' folder</error>");
                }

                if (!file_put_contents($project->getPath() . '/' . $default_locale . '/index.md', $this->getProjectIndexMd())) {
                    $output->writeln("<error>Failed to create '$default_locale/index.md' folder</error>");
                }
            } else {
                if (!file_put_contents($project->getPath() . '/index.md', $this->getProjectIndexMd())) {
                    $output->writeln("<error>Failed to create 'index.md' folder</error>");
                }
            }

            foreach (['books', 'releases', 'videos', 'whats_new'] as $what_to_make) {
                if ($default_locale) {
                    $what_to_make = "$default_locale/$what_to_make";
                }

                if (!mkdir($project->getPath() . '/' . $what_to_make)) {
                    $output->writeln("<error>Failed to create '$what_to_make' folder</error>");
                }
            }

            if (!mkdir($project->getPath() . '/temp')) {
                $output->writeln('<error>Failed to create /temp folder</error>');
            }

            if (!mkdir($project->getPath() . '/build')) {
                $output->writeln('<error>Failed to create /build folder</error>');
            }
        }
    }

    /**
     * Return content of the proejct index.md file.
     *
     * @return string
     */
    private function getProjectIndexMd()
    {
        $options = [
            'title' => 'Full Project Name',
            'description' => 'My Awesome Project',
        ];

        $result = '';

        foreach ($options as $k => $v) {
            $result .= $k . ': ' . $v . "\n";
        }

        return "{$result}\n================================================================\n\nContent for index page";
    }
}
