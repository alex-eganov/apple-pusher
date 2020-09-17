<?php

namespace bIbI4k0\ApplePusher\Payload;

/**
 * Class AbstractPayload
 * @package bIbI4k0\ApplePusher\Types
 */
abstract class AbstractPayload implements PayloadInterface
{
    /**
     * @var array
     */
    private $apsData = [];

    /**
     * @var array
     */
    private $customData = [];

    /**
     * @param string $key
     * @param $value
     */
    protected function setApsValue(string $key, $value): void
    {
        if ($value === null) {
            unset($this->apsData[$key]);
            return;
        }

        $this->apsData[$key] = $value;
    }

    /**
     * Set badge for the app icon. Specify 0 for remove badge. Specify null for dont change current badge.
     * Default state is null.
     *
     * @param int|null $count
     * @return static
     */
    public function setBadge(int $count = null): self
    {
        $this->setApsValue('badge', $count !== null && $count >= 0 ? $count : null);
        return $this;
    }

    /**
     * APNS doc: The notification’s type. This string must correspond to the identifier
     * of one of the UNNotificationCategory objects you register at launch time.
     *
     * @param string|null $category
     * @return static
     */
    public function setCategory(string $category = null): self
    {
        $this->setApsValue('category', $category);
        return $this;
    }

    /**
     * Assoc array merged with payload on the root level
     *
     * @param array $data
     * @return static
     */
    public function setCustomData(array $data): self
    {
        unset($data['aps']);
        $this->customData = $data;
        return $this;
    }

    /**
     * Returns assoc array for aps key of the payload
     *
     * @return array
     */
    protected function getApsData(): array
    {
        return $this->apsData;
    }

    /**
     * Returns custom data assoc array
     *
     * @return array
     */
    protected function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * @inheritDoc
     */
    abstract public function getType(): string;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge_recursive(
            $this->getCustomData(),
            ['aps' => $this->getApsData()]
        );
    }
}