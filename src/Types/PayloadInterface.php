<?php

namespace bIbI4k0\ApplePusher\Types;

use JsonSerializable;

/**
 * Interface PayloadInterface
 * @package bIbI4k0\ApplePusher\Types
 */
interface PayloadInterface extends JsonSerializable
{
    public const TYPE_ALERT = 'alert';
    public const TYPE_BACKGROUND = 'background';

    /**
     * @return string
     */
    public function getType(): string;
}
