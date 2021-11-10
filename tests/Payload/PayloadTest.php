<?php

namespace bIbI4k0\ApplePusher\Tests\Payload;

use bIbI4k0\ApplePusher\Tests\Stubs\StubPayload;
use PHPUnit\Framework\TestCase;

/**
 * Class PayloadTest
 * @package Tests\Types
 */
class PayloadTest extends TestCase
{
    public function testPayloadIsEmpty(): void
    {
        $payload = new StubPayload();

        $data = $payload->jsonSerialize();

        self::assertArrayHasKey('aps', $data);
        self::assertEmpty($data['aps']);
    }

    /**
     * @depends testPayloadIsEmpty
     */
    public function testCategory(): void
    {
        $categoryName = 'test category';

        $payload = new StubPayload();
        $payload->setCategory($categoryName);

        $aps = $payload->jsonSerialize()['aps'];

        self::assertArrayHasKey('category', $aps);
        self::assertEquals($categoryName, $aps['category']);

        $payload->setCategory(null);
        $aps = $payload->jsonSerialize()['aps'];
        self::assertArrayNotHasKey('category', $aps);
    }

    /**
     * @depends      testPayloadIsEmpty
     * @dataProvider badgeDataProvider
     *
     * @param int|null $badge
     * @param int|string $expected
     */
    public function testBadge(int $badge = null, $expected = 'not set'): void
    {
        $payload = new StubPayload();
        $payload->setBadge($badge);

        $aps = $payload->jsonSerialize()['aps'];
        $actual = array_key_exists('badge', $aps) ? $badge : 'not set';

        self::assertEquals($expected, $actual);
    }

    public function badgeDataProvider(): array
    {
        return [
            'badge set to null, => key dont exists' => [null, 'not set'],
            'badge lt 0, => key dont exists' => [-123, 'not set'],
            'badge eq 0, => key exists, val eq 0' => [0, 0],
            'badge gt 0, => key exists' => [12, 12],
        ];
    }

    /**
     * @depends testPayloadIsEmpty
     */
    public function testCustomData(): void
    {
        $customData = ['custom 1' => 123, 'custom 2' => 'test'];
        $payload = new StubPayload();
        $payload->setCustomData($customData);

        $data = $payload->jsonSerialize();

        unset($data['aps']);
        self::assertEquals($customData, $data);
    }

    /**
     * @depends testCustomData
     */
    public function testCustomDataDontOverrideApsKeyInPayload(): void
    {
        $apsValue = ['override aps'];
        $customData = ['custom 1' => 123, 'custom 2' => 'test', 'aps' => $apsValue];
        $payload = new StubPayload();
        $payload->setCustomData($customData);

        $data = $payload->jsonSerialize()['aps'];

        self::assertIsArray($data);
        self::assertNotEquals($apsValue, $data);
    }

    public function testSetSound(): void
    {
        $soundFile = 'icq.mp3';

        $payload = new StubPayload();
        $payload->setSound($soundFile);

        $data = $payload->jsonSerialize()['aps'];

        self::assertArrayHasKey('sound', $data);
        self::assertEquals($soundFile, $data['sound']);

        // drop sound to default
        $payload->setSound();
        $data = $payload->jsonSerialize()['aps'];
        self::assertArrayNotHasKey('sound', $data);
    }

    public function testSetSoundDict(): void
    {
        $soundFile = 'icq.mp3';
        $isCritical = true;
        $volume = 1.2;

        $payload = new StubPayload();
        $payload->setSoundDict($soundFile, $isCritical, $volume);

        $data = $payload->jsonSerialize()['aps'];

        self::assertArrayHasKey('sound', $data);
        self::assertIsArray($data['sound']);

        $soundData = $data['sound'];
        self::assertEquals($soundFile, $soundData['name']);
        self::assertEquals(1.0, $soundData['volume']);
        self::assertEquals((int)$isCritical, $soundData['critical']);
    }
}
