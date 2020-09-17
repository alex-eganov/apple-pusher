<?php

namespace Tests\Types;

use bIbI4k0\ApplePusher\Types\AlertPayload;
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
        $this->assertArrayHasKey('aps', $json);

        $apsData = $json['aps'];
        $this->assertArrayHasKey('alert', $apsData);

        $alertData = $apsData['alert'];
        $this->assertArrayHasKey('title', $alertData);
        $this->assertArrayNotHasKey('subtitle', $alertData);
        $this->assertArrayNotHasKey('text', $alertData);
        $this->assertEquals($title, $alertData['title']);
    }

    public function testSubtitle(): void
    {
        $title = 'test title';
        $subTitle = 'test subtitle';
        $alert = new AlertPayload($title, $subTitle);

        $alertData = $alert->jsonSerialize()['aps']['alert'];

        $this->assertArrayHasKey('title', $alertData);
        $this->assertArrayHasKey('subtitle', $alertData);
        $this->assertArrayNotHasKey('text', $alertData);
    }

    public function testText(): void
    {
        $title = 'test title';
        $subTitle = 'test subtitle';
        $body = 'test body';
        $alert = new AlertPayload($title, $subTitle, $body);

        $alertData = $alert->jsonSerialize()['aps']['alert'];

        $this->assertArrayHasKey('title', $alertData);
        $this->assertArrayHasKey('subtitle', $alertData);
        $this->assertArrayHasKey('body', $alertData);
    }
}