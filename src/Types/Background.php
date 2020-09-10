<?php

namespace bIbI4k0\ApplePusher\Types;

/**
 * Class Background
 * @package bIbI4k0\ApplePusher\Types
 */
class Background implements PayloadInterface
{
    private array $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

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
    public function jsonSerialize(): array
    {
        return array_merge($this->getData(), [
            'apns' => [
                'content-available' => 1
            ]
        ]);
    }
}
