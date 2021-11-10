<?php

namespace bIbI4k0\ApplePusher;

use bIbI4k0\ApplePusher\Payload\PayloadInterface;
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
    private string $deviceToken;

    /**
     * @var PayloadInterface
     */
    private PayloadInterface $payload;

    /*
     * array
     */
    private array $options = [];

    /**
     * @param string $deviceToken
     * @param PayloadInterface $payload
     */
    public function __construct(string $deviceToken, PayloadInterface $payload)
    {
        $this->deviceToken = $deviceToken;
        $this->setPayload($payload);
        $this->generateUuid();
    }

    /**
     * Set unique id of the push
     */
    private function generateUuid(): void
    {
        $this->setOption('id', Uuid::uuid4()->toString());
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function setOption(string $key, string $value): void
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
        $instance->generateUuid();

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
        return $this->options['id'];
    }

    /**
     * APNS doc: The date at which the notification is no longer valid.
     *
     * @param int $utcTime
     * @return static
     */
    public function setExpiration(int $utcTime): self
    {
        $this->setOption('expiration', (string)$utcTime);
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
        $this->setOption('priority', (string)$level);
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
        $this->setOption('topic', $topicName);
        return $this;
    }

    /**
     * @param PayloadInterface $payload
     * @return static
     */
    public function setPayload(PayloadInterface $payload): self
    {
        $this->payload = $payload;
        $this->setOption('type', $payload->getType());
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
