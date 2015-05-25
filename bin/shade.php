<?php

  /**
   * Bootstrap command line application
   */

  date_default_timezone_set('UTC');

  require dirname(__DIR__) . '/vendor/autoload.php';

  use Symfony\Component\Console\Application;

  $application = new Application('Shade', file_get_contents(dirname(__DIR__) . '/VERSION'));

  foreach (new DirectoryIterator(dirname(__DIR__) . '/src/ActiveCollab/Shade/Command') as $file) {
    if ($file->isFile()) {
      $class_name = ('\\ActiveCollab\\Shade\\Command\\' . $file->getBasename('.php'));

      if (!(new ReflectionClass($class_name))->isAbstract()) {
        $application->add(new $class_name);
      }
    }
  }

  $application->run();