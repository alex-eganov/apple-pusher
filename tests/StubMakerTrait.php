<?php

namespace Tests;

use bIbI4k0\ApplePusher\Payload\AlertPayload;
use bIbI4k0\ApplePusher\Payload\PayloadInterface;
use bIbI4k0\ApplePusher\Push;

/**
 * Trait StubMakerTrait
 * @package Tests
 */
trait StubMakerTrait
{
    /**
     * @param PayloadInterface $payload
     * @param string $token
     *
     * @return Push
     */
    protected function makePush(PayloadInterface $payload, string $token = 'test token'): Push
    {
        return new Push($token, $payload);
    }

    /**
     * @param string $title
     *
     * @return AlertPayload
     */
    protected function makeAlertPayload(string $title = 'test title'): AlertPayload
    {
        return new AlertPayload($title);
    }
}
