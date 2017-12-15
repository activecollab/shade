<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab;

use ActiveCollab\Shade\Element\Element;
use ActiveCollab\Shade\Error\TempNotFoundError;
use ActiveCollab\Shade\Error\ThemeNotFoundError;
use ActiveCollab\Shade\Plugin\DisqusPlugin;
use ActiveCollab\Shade\Plugin\GoogleAnalyticsPlugin;
use ActiveCollab\Shade\Plugin\GoogleTagManagerPlugin;
use ActiveCollab\Shade\Plugin\LiveChatPlugin;
use ActiveCollab\Shade\Plugin\Plugin;
use ActiveCollab\Shade\Project;
use ActiveCollab\Shade\SmartyHelpers;
use ActiveCollab\Shade\Theme;
use Exception;
use Hyperlight\Hyperlight;
use Michelf\MarkdownExtra;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionMethod;
use Smarty;
use URLify;

/**
 * Main class for interaction with Shade projects.
 */
final class Shade
{
    /**
     * @param  string             $name
     * @return Theme
     * @throws ThemeNotFoundError
     */
    public static function getBuildTheme($name)
    {
        $theme_path = __DIR__ . "/Shade/Themes/$name";

        if ($theme_path && is_dir($theme_path)) {
            return new Theme($theme_path);
        } else {
            throw new ThemeNotFoundError($name, $theme_path);
        }
    }

    /**
     * @var Smarty
     */
    private static $smarty = false;

    /**
     * Initialize Smarty.
     *
     * @param  Project           $project
     * @param  Theme             $theme
     * @return Smarty
     * @throws TempNotFoundError
     * @throws \SmartyException
     */
    public static function &initSmarty(Project &$project, Theme $theme)
    {
        if (self::$smarty === false) {
            self::$smarty = new Smarty();

            $temp_path = $project->getTempPath();

            if (is_dir($temp_path)) {
                self::$smarty->setCompileDir($temp_path);
            } else {
                throw new TempNotFoundError($temp_path);
            }

            self::$smarty->setTemplateDir($theme->getPath() . '/templates');
            self::$smarty->compile_check = true;
            self::$smarty->left_delimiter = '<{';
            self::$smarty->right_delimiter = '}>';
            self::$smarty->registerFilter('variable', '\ActiveCollab\Shade::clean'); // {$foo nofilter}

            $helper_class = new ReflectionClass('\ActiveCollab\Shade\SmartyHelpers');

            foreach ($helper_class->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC) as $method) {
                $method_name = $method->getName();

                if (substr($method_name, 0, 6) === 'block_') {
                    self::$smarty->registerPlugin('block', substr($method_name, 6), ['\ActiveCollab\Shade\SmartyHelpers', $method_name]);
                } elseif (substr($method_name, 0, 9) === 'function_') {
                    self::$smarty->registerPlugin('function', substr($method_name, 9), ['\ActiveCollab\Shade\SmartyHelpers', $method_name]);
                };
            }

            SmartyHelpers::setDefaultLocale($project->getDefaultLocale());

            self::$smarty->assign([
                'project' => $project,
                'default_locale' => $project->getDefaultLocale(),
                'copyright' => $project->getConfigurationOption('copyright', '--UNKNOWN--'),
                'copyright_since' => $project->getConfigurationOption('copyright_since'),
                'page_level' => 0,
                'plugins' => self::getPlugins($project),
            ]);
        }

        return self::$smarty;
    }

    /**
     * Return prepared Smarty instance.
     *
     * @return Smarty
     */
    public static function &getSmarty()
    {
        return self::$smarty;
    }

    /**
     * Return available plugins.
     *
     * @param  Project  $project
     * @return Plugin[]
     */
    public static function getPlugins(Project &$project)
    {
        return [
            new DisqusPlugin($project),
            new GoogleAnalyticsPlugin($project),
            new GoogleTagManagerPlugin($project),
            new LiveChatPlugin($project),
        ];
    }

    /**
     * @var array
     */
    private static $todo = [];

    /**
     * Return all to-do items.
     */
    public static function getTodo()
    {
        return self::$todo;
    }

    /**
     * Method that is used for collecting to-do items.
     *
     * @param string          $message
     * @param Project|Element $element
     */
    public static function recordTodo($message, $element)
    {
        if ($element instanceof Element) {
            $path = $element->getPath();
        } elseif ($element instanceof Project) {
            $path = $element->getPath();
        } else {
            $path = '-UNKNOWN-';
        }

        self::$todo[] = ['message' => $message, 'file' => $path];
    }

    /**
     * Check if we are in testing mode.
     *
     * @return bool
     */
    public static function isTesting()
    {
        return true;
    }

// ---------------------------------------------------
//  Utilities
// ---------------------------------------------------

    /**
     * Returns true if $version is a valid angie application version number.
     *
     * @param  string $version
     * @return bool
     */
    public static function isValidVersionNumber($version)
    {
        if (strpos($version, '.') !== false) {
            $parts = explode('.', $version);

            if (count($parts) == 3) {
                foreach ($parts as $part) {
                    if (!is_numeric($part)) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Return a message.
     *
     * @param  string $message
     * @return string
     */
    public static function lang($message)
    {
        return function_exists('gettext') ? gettext($message) : $message;
    }

    /**
     * Equivalent to htmlspecialchars(), but allows &#[0-9]+ (for unicode).
     *
     * @param  string    $str
     * @return string
     * @throws Exception
     */
    public static function clean($str)
    {
        if (is_scalar($str)) {
            $str = preg_replace('/&(?!#(?:[0-9]+|x[0-9A-F]+);?)/si', '&amp;', $str);
            $str = str_replace(['<', '>', '"'], ['&lt;', '&gt;', '&quot;'], $str);

            return $str;
        } elseif ($str === null) {
            return '';
        } else {
            throw new Exception('Input needs to be scalar value');
        }
    }

    /**
     * Returns an underscore-syntaxed ($like_this_dear_reader) version of the $camel_cased_word.
     *
     * @param  string $camel_cased_word
     * @return string
     */
    public static function underscore($camel_cased_word)
    {
        $camel_cased_word = preg_replace('/([A-Z]+)([A-Z])/', '\1_\2', $camel_cased_word);

        return strtolower(preg_replace('/([a-z])([A-Z])/', '\1_\2', $camel_cased_word));
    }

    /**
     * Return slug from string.
     *
     * @static
     * @param               $string
     * @param  string       $space
     * @return mixed|string
     */
    public static function slug($string, $space = '-')
    {
        $string = URLify::transliterate($string);

        $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string);
        $string = strtolower($string);
        $string = str_replace(' ', $space, $string);

        while (strpos($string, '--') !== false) {
            $string = str_replace('--', '-', $string);
        }

        return trim($string);
    }

    /**
     * Open HTML tag.
     *
     * @param  string               $name
     * @param  array                $attributes
     * @param  callable|string|null $content
     * @return string
     */
    public static function htmlTag($name, $attributes = null, $content = null)
    {
        if ($attributes) {
            $result = "<$name";

            foreach ($attributes as $k => $v) {
                if ($k) {
                    if (is_bool($v)) {
                        if ($v) {
                            $result .= " $k";
                        }
                    } else {
                        $result .= ' ' . $k . '="' . ($v ? self::clean($v) : $v) . '"';
                    }
                }
            }

            $result .= '>';
        } else {
            $result = "<$name>";
        }

        if ($content) {
            if (is_callable($content)) {
                $result .= call_user_func($content);
            } else {
                $result .= $content;
            }

            $result .= "</$name>";
        }

        return $result;
    }

    /**
     * @param  string $markdown
     * @return string
     */
    public static function markdownToHtml($markdown)
    {
        return MarkdownExtra::defaultTransform($markdown);
    }

    /**
     * Renders the full preview with line numbers and all necessary DOM.
     *
     * @param  string $content
     * @param  string $syntax
     * @return string
     */
    public static function highlightCode($content, $syntax)
    {
        $content = trim($content);

        if ($syntax) {
            $hyperlight = new Hyperlight(strtolower($syntax));
            $content = $hyperlight->render($content);
        } else {
            $content = self::clean($content);
        }

        $number_of_lines = count(explode("\n", $content));

        $output = '<div class="syntax_higlighted source-code">';
        $output .= '<div class="syntax_higlighted_line_numbers lines"><pre>' . implode("\n", range(1, $number_of_lines)) . '</pre></div>';
        $output .= '<div class="syntax_higlighted_source"><pre>' . $content . '</pre></div>';
        $output .= '</div>';

        return $output;
    }

    /**
     * @param string        $source_path
     * @param string        $target_path
     * @param callable|null $on_create_dir
     * @param callable|null $on_copy_file
     */
    public static function copyDir($source_path, $target_path, $on_create_dir = null, $on_copy_file = null)
    {
        if (!is_dir($target_path)) {
            mkdir($target_path, 0755);
        }

        /**
         * @var RecursiveDirectoryIterator $iterator
         */
        foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source_path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {

            if ($item->isDir()) {
                mkdir($target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname());

                if ($on_create_dir) {
                    call_user_func($on_create_dir, $target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
                }
            } else {
                copy($item, $target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname());

                if ($on_copy_file) {
                    call_user_func($on_copy_file, $item->getPath(), $target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
                }
            }
        }
    }

    /**
     * Create a new directory at $path.
     *
     * @param string        $path
     * @param callable|null $on_item_created
     */
    public static function createDir($path, $on_item_created)
    {
        if (mkdir($path)) {
            if ($on_item_created) {
                call_user_func($on_item_created, $path);
            }
        }
    }

    /**
     * Clear all files and subfolders from $path.
     *
     * @param string        $path
     * @param callable|null $on_item_deleted
     * @param bool          $is_subpath
     */
    public static function clearDir($path, $on_item_deleted = null, $is_subpath = false)
    {
        if (is_link($path)) {
            // Don't follow links
        } elseif (is_file($path)) {
            if (unlink($path)) {
                if ($on_item_deleted) {
                    call_user_func($on_item_deleted, $path);
                }
            }
        } elseif (is_dir($path)) {
            foreach (glob(rtrim($path, '/') . '/*') as $index => $subdir_path) {
                self::clearDir($subdir_path, $on_item_deleted, true);
            }

            if ($is_subpath && rmdir($path)) {
                if ($on_item_deleted) {
                    call_user_func($on_item_deleted, $path);
                }
            }
        }
    }

    /**
     * Write a new file with given content.
     *
     * @param string        $path
     * @param string        $content
     * @param callable|null $on_file_created
     */
    public static function writeFile($path, $content, $on_file_created = null)
    {
        $overwrite = file_exists($path);

        if (file_put_contents($path, $content) && $on_file_created) {
            call_user_func($on_file_created, $path, $overwrite);
        }
    }
}
