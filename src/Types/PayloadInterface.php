<?php

namespace bIbI4k0\ApplePusher\Types;

use JsonSerializable;

/**
 * Interface PayloadInterface
 * @package ApplePusher\Types
 */
interface PayloadInterface extends JsonSerializable
{
    public const TYPE_ALERT = 'alert';

    /**
     * @return string
     */
    public function getType(): string;
}
