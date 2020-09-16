<?php

namespace Tests;

use bIbI4k0\ApplePusher\Push;
use bIbI4k0\ApplePusher\Response;
use bIbI4k0\ApplePusher\ResponseStatus;
use bIbI4k0\ApplePusher\Types\Alert;
use PHPUnit\Framework\TestCase;

/**
 * Class ResponseTest
 * @package Tests
 */
class ResponseTest extends TestCase
{
    private const TEST_TOKEN = 'test token';

    /**
     * @param int $httpCode
     * @param string|null $reason
     * @param Push|null $push
     * @return Response
     */
    private function makeResponse(
        int $httpCode = ResponseStatus::STATUS_OK,
        string $reason = null,
        Push $push = null
    ): Response {
        $push = $push ?: new Push(self::TEST_TOKEN, new Alert('some title'));

        return new Response($httpCode, $push, $reason);
    }

    public function testIsOk(): void
    {
        $resp = $this->makeResponse(ResponseStatus::STATUS_OK);

        $this->assertTrue($resp->isOk());
    }

    public function testIsNotOk(): void
    {
        $reason = 'test reason';
        $resp = $this->makeResponse(ResponseStatus::STATUS_BAD_REQUEST, $reason);

        $this->assertFalse($resp->isOk());
        $this->assertEquals($resp->getReason(), $reason);
    }
}