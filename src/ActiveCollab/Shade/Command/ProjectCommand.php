<?php

  namespace ActiveCollab\Shade\Command;

  use Symfony\Component\Console\Command\Command, Symfony\Component\Console\Input\InputInterface, Symfony\Component\Console\Output\OutputInterface;

  /**
   * Class description
   *
   * @package
   * @subpackage
   */
  class Project extends Command
  {
    function configure()
    {
      $this->setName('project')->addArgument('name')->setDescription('Show project details or create a new project');
    }
  }