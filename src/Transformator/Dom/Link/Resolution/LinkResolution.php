<?php

/*
 * This file is part of the Shade project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Shade\Transformator\Dom\Link\Resolution;

use ActiveCollab\Shade\Ability\BuildableInterface;
use LogicException;

class LinkResolution implements LinkResolutionInterface
{
    private $target;
    private $notFoundMessage;

    public function __construct(?BuildableInterface $target, string $notFoundMessage = null)
    {
        if (empty($target) && empty($notFoundMessage)) {
            throw new LogicException('When target is not found, proper Not Found message is required.');
        }

        $this->target = $target;
        $this->notFoundMessage = $notFoundMessage;
    }

    public function isFound(): bool
    {
        return !empty($this->target);
    }

    public function getTarget(): ?BuildableInterface
    {
        return $this->target;
    }

    public function getNotFoundMessage(): string
    {
        return $this->notFoundMessage;
    }
}
