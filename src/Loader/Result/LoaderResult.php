<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Loader\Result;

class LoaderResult implements LoaderResultInterface
{
    private $indexFilePath;
    private $properties;
    private $body;

    public function __construct(string $indexFilePath, array $properties, string $body)
    {
        $this->indexFilePath = $indexFilePath;
        $this->properties = $properties;
        $this->body = $body;
    }

    public function getIndexFilePath(): string
    {
        return $this->indexFilePath;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $name, string $default = null): ?string
    {
        return $this->properties[$name] ?? $default;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
