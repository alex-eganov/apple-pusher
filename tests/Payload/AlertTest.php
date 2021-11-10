<?php

namespace bIbI4k0\ApplePusher\Tests\Payload;

use bIbI4k0\ApplePusher\Payload\AlertPayload;
use PHPUnit\Framework\TestCase;

/**
 * Class AlertTest
 * @package Tests\Types
 */
class AlertTest extends TestCase
{
    public function testTitle(): void
    {
        $title = 'test title';
        $alert = new AlertPayload($title);

        $json = $alert->jsonSerialize();
        self::assertArrayHasKey('aps', $json);

        $apsData = $json['aps'];
        self::assertArrayHasKey('alert', $apsData);

        $alertData = $apsData['alert'];
        self::assertArrayHasKey('title', $alertData);
        self::assertArrayNotHasKey('subtitle', $alertData);
        self::assertArrayNotHasKey('text', $alertData);
        self::assertEquals($title, $alertData['title']);
    }

    public function testSubtitle(): void
    {
        $title = 'test title';
        $subTitle = 'test subtitle';
        $alert = new AlertPayload($title, $subTitle);

        $alertData = $alert->jsonSerialize()['aps']['alert'];

        self::assertArrayHasKey('title', $alertData);
        self::assertArrayHasKey('subtitle', $alertData);
        self::assertArrayNotHasKey('text', $alertData);
    }

    public function testText(): void
    {
        $title = 'test title';
        $subTitle = 'test subtitle';
        $body = 'test body';
        $alert = new AlertPayload($title, $subTitle, $body);

        $alertData = $alert->jsonSerialize()['aps']['alert'];

        self::assertArrayHasKey('title', $alertData);
        self::assertArrayHasKey('subtitle', $alertData);
        self::assertArrayHasKey('body', $alertData);
    }

    public function testAlertAsString(): void
    {
        $title = 'test title';
        $subTitle = 'test subtitle';
        $body = 'test body';
        $alert = new AlertPayload($title, $subTitle, $body);

        $alert->setAlertAsString(true);

        $alertData = $alert->jsonSerialize()['aps']['alert'];

        self::assertEquals('test title', $alertData);
    }
}