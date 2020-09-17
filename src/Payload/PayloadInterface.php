<?php

namespace bIbI4k0\ApplePusher\Payload;

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
     * Returns type of the notification. Possible values are described in apns documentation.
     * This library currently implements only two of them: alert, background.
     *
     * @return string
     */
    public function getType(): string;
}
