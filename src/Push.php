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
    private string $deviceToken;
    private string $uuid;
    private PayloadInterface $payload;
    private array $options = [];

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
    public function withDevice(string $newDeviceToken): self
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
    public function getDevice(): string
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
     */
    public function setExpiration(int $utcTime): void
    {
        $this->addOption('expiration', (string)$utcTime);
    }

    /**
     * APNS doc: The priority of the notification. If you omit this header, APNs sets the notification priority to 10.
     * Specify 10 to send the notification immediately.
     *
     * @param int $level
     */
    public function setPriority(int $level): void
    {
        $this->addOption('priority', (string)$level);
    }

    /**
     * APNS doc: The topic for the notification. In general, the topic is your app’s bundle ID,
     * but it may have a suffix based on the push notification’s type.
     *
     * @param string $topicName
     */
    public function setTopic(string $topicName): void
    {
        $this->addOption('topic', $topicName);
    }

    /**
     * @param PayloadInterface $payload
     */
    public function setPayload(PayloadInterface $payload): void
    {
        $this->payload = $payload;
        $this->addOption('type', $payload->getType());
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'aps' => $this->payload,
        ];
    }
}