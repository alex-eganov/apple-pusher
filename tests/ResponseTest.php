<?php

namespace Tests;

use bIbI4k0\ApplePusher\Exception\ResponseParseException;
use bIbI4k0\ApplePusher\Push;
use bIbI4k0\ApplePusher\Response\Response;
use bIbI4k0\ApplePusher\Response\StatusCodes;
use PHPUnit\Framework\TestCase;

/**
 * Class ResponseTest
 * @package Tests
 */
class ResponseTest extends TestCase
{
    use StubMakerTrait;

    private const TEST_TOKEN = 'test token';

    /**
     * @param int $httpCode
     * @param string|null $reason
     * @param Push|null $push
     * @return Response
     */
    private function makeResponse(
        int $httpCode = StatusCodes::OK,
        string $reason = null,
        Push $push = null
    ): Response {
        $push = $push ?: $this->makePush($this->makeAlertPayload());

        return new Response($httpCode, $push, $reason);
    }

    public function testIsOk(): void
    {
        $resp = $this->makeResponse();

        self::assertTrue($resp->isOk());
    }

    public function testIsNotOk(): void
    {
        $reason = 'test reason';
        $resp = $this->makeResponse(StatusCodes::BAD_REQUEST, $reason);

        self::assertFalse($resp->isOk());
        self::assertEquals($resp->getReason(), $reason);
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

        Response::fromJson($json, StatusCodes::OK, $push);
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

    /**
     * @throws ResponseParseException
     */
    public function testFromJsonCreateResponseIfJsonEmpty(): void
    {
        $json = '';
        $push = $this->makePush($this->makeAlertPayload());

        $response = Response::fromJson($json, StatusCodes::OK, $push);

        self::assertTrue($response->isOk());
        self::assertNull($response->getReason());
    }

    /**
     * @throws ResponseParseException
     */
    public function testFromJsonCreateResponseWithValidReason(): void
    {
        $reason = 'test reason';
        $json = sprintf('{"reason":"%s"}', $reason);
        $push = $this->makePush($this->makeAlertPayload());

        $response = Response::fromJson($json, StatusCodes::BAD_REQUEST, $push);

        self::assertFalse($response->isOk());
        self::assertEquals($reason, $response->getReason());
    }
}