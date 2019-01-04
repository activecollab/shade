<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Loader;

use ActiveCollab\Shade\Ability\LoadableInterface;
use ActiveCollab\Shade\Error\ElementFileNotFoundError;
use ActiveCollab\Shade\Loader\Result\LoaderResult;
use ActiveCollab\Shade\Loader\Result\LoaderResultInterface;
use ActiveCollab\Shade\Shade;

class Loader implements LoaderInterface
{
    private $propertiesSeparator;

    public function __construct(string $propertiesSeparator = LoadableInterface::PROPERTIES_SEPARATOR)
    {
        $this->propertiesSeparator = $propertiesSeparator;
    }

    public function load(LoadableInterface $loadableElement): LoaderResultInterface
    {
        $index_file_path = $loadableElement->getIndexFilePath();

        if (is_file($index_file_path)) {
            $body = file_get_contents($index_file_path);

            $separator_pos = strpos($body, $this->propertiesSeparator);

            if ($separator_pos === false) {
                if (substr($body, 0, 1) == '*') {
                    $properties_string = $body;
                    $body = '';
                } else {
                    $properties_string = '';
                }
            } else {
                $properties_string = trim(substr($body, 0, $separator_pos));
                $body = trim(substr($body, $separator_pos + strlen($this->propertiesSeparator)));
            }

            return new LoaderResult(
                $index_file_path,
                $properties_string ? $this->parseProperties($properties_string) : [],
                trim($body)
            );
        } else {
            throw new ElementFileNotFoundError($index_file_path);
        }
    }

    private function parseProperties(string $propertiesString): array
    {
        $properties = [];

        $properties_lines = explode("\n", $propertiesString);

        if (count($properties_lines)) {
            foreach ($properties_lines as $properties_line) {
                $properties_line = trim(trim($properties_line, '*')); // Clean up

                if ($properties_line) {
                    $colon_pos = strpos($properties_line, ':');

                    if ($colon_pos !== false) {
                        $property_name = trim(substr($properties_line, 0, $colon_pos));
                        $property_value = trim(substr($properties_line, $colon_pos + 1));

                        $properties[$this->normalizePropertyName($property_name)] = $property_value;
                    }
                }
            }
        }

        return $properties;
    }

    private function normalizePropertyName(string $property_name): string
    {
        return Shade::underscore(str_replace(' ', '', $property_name));
    }
}
