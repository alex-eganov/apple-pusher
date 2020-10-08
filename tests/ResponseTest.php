<?php

namespace Tests;

use bIbI4k0\ApplePusher\Exception\ResponseParseException;
use bIbI4k0\ApplePusher\Payload\PayloadInterface;
use bIbI4k0\ApplePusher\Push;
use bIbI4k0\ApplePusher\Response;
use bIbI4k0\ApplePusher\ResponseStatus;
use bIbI4k0\ApplePusher\Payload\AlertPayload;
use PHPUnit\Framework\TestCase;
use Tests\Types\StubPayload;

/**
 * Class ResponseTest
 * @package Tests
 */
class ResponseTest extends TestCase
{
    private const TEST_TOKEN = 'test token';

    /**
     * @param PayloadInterface $payload
     * @return Push
     */
    private function makePush(PayloadInterface $payload): Push
    {
        return new Push(self::TEST_TOKEN, $payload);
    }

    /**
     * @param string $title
     * @return AlertPayload
     */
    private function makeAlertPayload(string $title = 'test title'): AlertPayload
    {
        return new AlertPayload($title);
    }

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
        $push = $push ?: $this->makePush($this->makeAlertPayload());

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

    /**
     * @param string $json
     *
     * @dataProvider fromJsonRaiseErrorDataProvider
     *
     * @throws ResponseParseException
     */
    public function testFromJsonRaiseError(string $json): void
    {
        $this->expectException(ResponseParseException::class);

        $push = $this->makePush($this->makeAlertPayload());

        Response::fromJson($json, ResponseStatus::STATUS_OK, $push);
    }

    /**
     * @return array
     */
    public function fromJsonRaiseErrorDataProvider(): array
    {
        return [
            'невалидный json 1' => ['{]'],
            'невалидный json 2' => ['{"sss"}'],
        ];
    }

    public function testFromJsonCreateResponseIfJsonEmpty(): void
    {
        $json = '';
        $push = $this->makePush($this->makeAlertPayload());

        $response = Response::fromJson($json, ResponseStatus::STATUS_OK, $push);

        $this->assertTrue($response->isOk());
        $this->assertNull($response->getReason());
    }

    public function testFromJsonCreateResponseWithValidReason(): void
    {
        $reason = 'test reason';
        $json = sprintf('{"reason":"%s"}', $reason);
        $push = $this->makePush($this->makeAlertPayload());

        $response = Response::fromJson($json, ResponseStatus::STATUS_BAD_REQUEST, $push);

        $this->assertFalse($response->isOk());
        $this->assertEquals($reason, $response->getReason());
    }
}