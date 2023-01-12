<?php

namespace bIbI4k0\ApplePusher\Tests;

use PHPUnit\Framework\TestCase;
use bIbI4k0\ApplePusher\Payload\BackgroundPayload;
use bIbI4k0\ApplePusher\Payload\PayloadInterface;
use bIbI4k0\ApplePusher\Tests\Stubs\StubMakerTrait;

/**
 * Class PushTest
 * @package Tests
 */
class PushTest extends TestCase
{
    use StubMakerTrait;

    private const TEST_TOKEN = 'test token';

    private const UUID_RE = '/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/i';

    /**
     * @param string $expected
     */
    private static function assertUUID(string $expected): void
    {
        self::assertMatchesRegularExpression(self::UUID_RE, $expected);
    }

    public function testGetOptions(): void
    {
        $push = $this->makePush($this->makeAlertPayload());
        $push->setTopic('topic');
        $push->setExpiration(100);
        $push->setPriority(10);

        $options = $push->getOptions();

        $expected = [
            'topic' => 'topic',
            'push-type' => PayloadInterface::TYPE_ALERT,
            'priority' => 10,
            'expiration' => 100,
        ];
        foreach ($expected as $name => $value) {
            self::assertArrayHasKey($name, $options);
            self::assertEquals($value, $options[$name]);
        }
        self::assertArrayHasKey('id', $options);
        self::assertUUID($options['id']);
    }

    public function testChangePayload(): void
    {
        $push = $this->makePush($this->makeAlertPayload());

        $options = $push->getOptions();
        self::assertEquals(PayloadInterface::TYPE_ALERT, $options['push-type']);

        $push->setPayload(new BackgroundPayload());
        $options = $push->getOptions();
        self::assertEquals(PayloadInterface::TYPE_BACKGROUND, $options['push-type']);
    }

    public function testCloneWithDevice(): void
    {
        $push = $this->makePush($this->makeAlertPayload());

        $anotherPush = $push->cloneWithDeviceToken('new token');

        self::assertNotSame($push, $anotherPush);
        self::assertEquals($push->getPayload()->jsonSerialize(), $anotherPush->getPayload()->jsonSerialize());
        self::assertNotEquals($push->getDeviceToken(), $anotherPush->getDeviceToken());
        self::assertNotEquals($push->getUuid(), $anotherPush->getUuid());
    }

    public function testPushUUID(): void
    {
        $push = $this->makePush($this->makeAlertPayload());
        self::assertUUID($push->getUuid());
    }
}