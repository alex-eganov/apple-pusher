<?php

namespace Tests\Types;

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

        $this->assertArrayHasKey('aps', $data);
        $this->assertEmpty($data['aps']);
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

        $this->assertArrayHasKey('category', $aps);
        $this->assertEquals($categoryName, $aps['category']);

        $payload->setCategory(null);
        $aps = $payload->jsonSerialize()['aps'];
        $this->assertArrayNotHasKey('category', $aps);
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

        $this->assertEquals($expected, $actual);
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
        $this->assertEquals($customData, $data);
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

        $this->assertIsArray($data);
        $this->assertNotEquals($apsValue, $data);
    }

    public function testSetSound(): void
    {
        $soundFile = 'icq.mp3';

        $payload = new StubPayload();
        $payload->setSound($soundFile);

        $data = $payload->jsonSerialize()['aps'];

        $this->assertArrayHasKey('sound', $data);
        $this->assertEquals($soundFile, $data['sound']);

        // drop sound to default
        $payload->setSound();
        $data = $payload->jsonSerialize()['aps'];
        $this->assertArrayNotHasKey('sound', $data);
    }

    public function testSetSoundDict(): void
    {
        $soundFile = 'icq.mp3';
        $isCritical = true;
        $volume = 1.2;

        $payload = new StubPayload();
        $payload->setSoundDict($soundFile, $isCritical, $volume);

        $data = $payload->jsonSerialize()['aps'];

        $this->assertArrayHasKey('sound', $data);
        $this->assertIsArray($data['sound']);

        $soundData = $data['sound'];
        $this->assertEquals($soundFile, $soundData['name']);
        $this->assertEquals(1.0, $soundData['volume']);
        $this->assertEquals((int)$isCritical, $soundData['critical']);
    }
}
