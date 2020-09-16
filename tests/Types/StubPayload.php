<?php

namespace Tests\Types;

use bIbI4k0\ApplePusher\Types\AbstractPayload;

/**
 * Class StubPayload
 * @package Tests\Types
 */
class StubPayload extends AbstractPayload
{
    public function getType(): string
    {
        return 'test';
    }
}
