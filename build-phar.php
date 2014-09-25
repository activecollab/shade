#!/usr/bin/env php
<?php

  if (php_sapi_name() != 'cli') {
    die('Please use CLI to run this script');
  }

  if (isset($argv[1]) && $argv[1]) {
    $phar_path = rtrim($argv[1], '/') . '/shade.phar';
  } else {
    $phar_path = __DIR__ . '/shade.phar';
  }

  require 'vendor/autoload.php';

  use Phine\Phar\Builder, Phine\Phar\Stub;

  $skip_if_found = [ '/.git', '/.svn', '/smarty/documentation', '/smarty/development', '/tests', '/Tests' ];
  $source_path_strlen = strlen(__DIR__);

  $builder = Builder::create($phar_path);

  $builder->addFile(__DIR__ . '/LICENSE', 'LICENESE');

  foreach ([ 'bin', 'src', 'vendor' ] as $dir_to_add) {
    /**
     * @var RecursiveDirectoryIterator[] $iterator
     */
    foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/' . $dir_to_add, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
      $pathname = $item->getPathname();
      $short_pathname = substr($pathname, $source_path_strlen + 1);

      foreach ($skip_if_found as $what) {
        if (strpos($pathname, $what) !== false) {
          continue 2;
        }
      }

      if ($item->isDir()) {
        $builder->addEmptyDir($short_pathname);
      } elseif($item->isFile()) {
        $builder->addFile($pathname, $short_pathname);
      }

      print "Adding $short_pathname\n";
    }
  }

////  // ---------------------------------------------------
////  //  Add /src
////  // ---------------------------------------------------
////
////  foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/src', RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
////    $pathname = $item->getPathname();
////    $short_pathname = substr($pathname, $source_path_strlen + 1);
////
////    if ($item->isDir()) {
////      $builder->addEmptyDir($short_pathname);
////    } elseif($item->isFile()) {
////      $builder->addFile($pathname, $short_pathname);
////    }
////
////    print "Adding $short_pathname\n";
////  }
////
////  return;
////
////  // ---------------------------------------------------
////  //  Add /vendor
////  // ---------------------------------------------------
////
////
////
////  /**
////   * @var RecursiveDirectoryIterator[] $iterator
////   */
////  foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/vendor', RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
////    $pathname = $item->getPathname();
////
////    foreach ($skip_if_found as $what) {
////      if (strpos($pathname, $what) !== false) {
////        continue 2;
////      }
////    }
////
////    if ($item->isDir()) {
////      $builder->addEmptyDir(substr($pathname, $source_path_strlen + 1));
////    } elseif($item->isFile()) {
////      $builder->addFile($pathname, substr($pathname, $source_path_strlen + 1));
////    }
////
////    print 'Adding ' . substr($pathname, $source_path_strlen + 1) . "\n";
////  }
//
//  return;

//  $builder->buildFromIterator(
//    Finder::create()
//      ->files()
//      ->name('*.php')
//      ->exclude('Tests')
//      ->in(__DIR__ . "/vendor")
//  );

  $builder->setStub(
    Stub::create()
      ->mapPhar('shade.phar')
      ->addRequire('bin/shade')
      ->getStub()
  );

