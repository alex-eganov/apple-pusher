<?php

namespace Tests;

use bIbI4k0\ApplePusher\Payload\AlertPayload;
use bIbI4k0\ApplePusher\Payload\BackgroundPayload;
use PHPUnit\Framework\TestCase;

/**
 * Class PushTest
 * @package Tests
 */
class PushTest extends TestCase
{
    use StubMakerTrait;

    private const TEST_TOKEN = 'test token';

    public function testGetOptions(): void
    {
        $push = $this->makePush($this->makeAlertPayload());
        $push->setTopic('topic');
        $push->setExpiration(100);
        $push->setPriority(10);

        $options = $push->getOptions();

        self::assertEquals([
            'topic' => 'topic',
            'type' => AlertPayload::TYPE_ALERT,
            'priority' => 10,
            'expiration' => 100,
        ], $options);
    }

    public function testChangePayload(): void
    {
        $push = $this->makePush($this->makeAlertPayload());

        $options = $push->getOptions();
        self::assertEquals(AlertPayload::TYPE_ALERT, $options['type']);

        $push->setPayload(new BackgroundPayload());
        $options = $push->getOptions();
        self::assertEquals(AlertPayload::TYPE_BACKGROUND, $options['type']);
    }

    public function testCloneWithDevice(): void
    {
        $push = $this->makePush($this->makeAlertPayload());

        $anotherPush = $push->cloneWithDeviceToken('new token');

        self::assertNotSame($push, $anotherPush);
        self::assertEquals($push->getPayload()->jsonSerialize(), $anotherPush->getPayload()->jsonSerialize());
        self::assertEquals($push->getOptions(), $anotherPush->getOptions());
        self::assertNotEquals($push->getDeviceToken(), $anotherPush->getDeviceToken());
        self::assertNotEquals($push->getUuid(), $anotherPush->getUuid());
    }

    public function testPushUUID(): void
    {
        $push = $this->makePush($this->makeAlertPayload());

        $isMatched = (bool)preg_match(
            "/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/i",
            $push->getUuid()
        );
        self::assertTrue($isMatched);
    }
}