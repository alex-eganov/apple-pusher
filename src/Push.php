<?php

namespace bIbI4k0\ApplePusher;

use bIbI4k0\ApplePusher\Types\PayloadInterface;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

/**
 * Class Push
 * @package bIbI4k0\ApplePusher
 */
class Push implements JsonSerializable
{
    /**
     * @var string
     */
    private $deviceToken;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var PayloadInterface
     */
    private $payload;

    /*
     * array
     */
    private $options = [];

    /**
     * @param string $deviceToken
     * @param PayloadInterface $payload
     */
    public function __construct(string $deviceToken, PayloadInterface $payload)
    {
        $this->deviceToken = $deviceToken;
        $this->setPayload($payload);
        $this->generateUUID();
    }

    /**
     * Generate and set new unique id for this Push
     */
    private function generateUUID(): void
    {
        $this->uuid = Uuid::uuid4()->toString();
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function addOption(string $key, string $value): void
    {
        $this->options[$key] = $value;
    }

    /**
     * Returns new instance with specified device token
     *
     * @param string $newDeviceToken
     * @return static
     */
    public function cloneWithDeviceToken(string $newDeviceToken): self
    {
        $instance = clone $this;
        $instance->deviceToken = $newDeviceToken;
        $instance->generateUUID();

        return $instance;
    }

    /**
     * Returns device token
     *
     * @return string
     */
    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }

    /**
     * Returns unique push id
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * APNS doc: The date at which the notification is no longer valid.
     *
     * @param int $utcTime
     * @return static
     */
    public function setExpiration(int $utcTime): self
    {
        $this->addOption('expiration', (string)$utcTime);
        return $this;
    }

    /**
     * APNS doc: The priority of the notification. If you omit this header, APNs sets the notification priority to 10.
     * Specify 10 to send the notification immediately.
     *
     * @param int $level
     * @return static
     */
    public function setPriority(int $level): self
    {
        $this->addOption('priority', (string)$level);
        return $this;
    }

    /**
     * APNS doc: The topic for the notification. In general, the topic is your app’s bundle ID,
     * but it may have a suffix based on the push notification’s type.
     *
     * @param string $topicName
     * @return static
     */
    public function setTopic(string $topicName): self
    {
        $this->addOption('topic', $topicName);
        return $this;
    }

    /**
     * @param PayloadInterface $payload
     * @return static
     */
    public function setPayload(PayloadInterface $payload): self
    {
        $this->payload = $payload;
        $this->addOption('type', $payload->getType());
        return $this;
    }

    public function getPayload(): PayloadInterface
    {
        return $this->payload;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->getPayload();
    }
}
