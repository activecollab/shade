<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

if (php_sapi_name() != 'cli') {
    die('Please use CLI to run this script');
}

print "Running composer install --no-dev --prefer-dist --optimize-autoloader...\n";
shell_exec('composer install --no-dev --prefer-dist --optimize-autoloader');

$version = isset($argv[1]) && $argv[1] ? $argv[1] : null;

if (!$version) {
    $version = trim(file_get_contents(__DIR__ . '/VERSION'));
}

$phar_path = __DIR__ . "/dist/shade-{$version}.phar";

if (is_file($phar_path)) {
    print "File '$phar_path' exists. Overwrite (y/n)?\n";

    if (strtolower(trim(fgets(STDIN))) === 'y') {
        unlink($phar_path);
    } else {
        die("Done, file kept...\n");
    }
}

require 'vendor/autoload.php';

use Phine\Phar\Builder, Phine\Phar\Stub;

$skip_if_found = ['/.git', '/.svn', '/smarty/documentation', '/smarty/development', '/tests', '/Tests'];
$source_path_strlen = strlen(__DIR__);

$builder = Builder::create($phar_path);

$builder->addFile(__DIR__ . '/LICENSE', 'LICENSE');
$builder->addFile(__DIR__ . '/VERSION', 'VERSION');

foreach (['bin', 'src', 'vendor'] as $dir_to_add) {
    /**
     * @var RecursiveDirectoryIterator[] $iterator
     */
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            __DIR__ . '/' . $dir_to_add,
            RecursiveDirectoryIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $pathname = $item->getPathname();
        $short_pathname = substr($pathname, $source_path_strlen + 1);

        foreach ($skip_if_found as $what) {
            if (strpos($pathname, $what) !== false) {
                continue 2;
            }
        }

        if ($item->isDir()) {
            $builder->addEmptyDir($short_pathname);
        } elseif ($item->isFile()) {
            $builder->addFile($pathname, $short_pathname);
        }

        print "Adding $short_pathname\n";
    }
}

$builder->setStub(
    Stub::create()
        ->mapPhar('shade.phar')
        ->addRequire('bin/shade.php')
        ->getStub()
);

print "\nRunning composer install...\n\n";
shell_exec('composer install --ignore-platform-reqs');

die("\n" . basename($phar_path) . ' created. SHA1 checksum: ' . sha1_file($phar_path) . "\n");
