<?php

namespace bIbI4k0\ApplePusher\Payload;

/**
 * Class BackgroundPayload
 * @package bIbI4k0\ApplePusher\Payload
 */
class BackgroundPayload extends AbstractPayload
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE_BACKGROUND;
    }

    /**
     * @return array
     */
    protected function getApsData(): array
    {
        return [
            'content-available' => 1,
        ];
    }
}
