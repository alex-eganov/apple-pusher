<?php

namespace bIbI4k0\ApplePusher\Payload;

/**
 * Class AbstractPayload
 * @package bIbI4k0\ApplePusher\Payload
 */
abstract class AbstractPayload implements PayloadInterface
{
    /**
     * @var array
     */
    private array $apsData = [];

    /**
     * @var array
     */
    private array $customData = [];

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
     * Set sound of this alert. Specify the string "default" to play the system sound.
     * Use this key for regular notifications.
     *
     * Specify null for drop any value.
     *
     * @param string|null $appSoundFile The name of a sound file in your app’s main bundle
     * or in the Library/Sounds folder of your app’s container directory.
     *
     * @return $this
     */
    public function setSound(string $appSoundFile = null): self
    {
        $this->setApsValue('sound', $appSoundFile);
        return $this;
    }

    /**
     * @param string $appSoundFile The name of a sound file in your app’s main bundle
     * or in the Library/Sounds folder of your app’s container directory.
     * @param bool $isCritical The critical alert flag
     * @param float $volume The volume for the critical alert’s sound. From 0.0(silent) to 1.0(full volume).
     *
     * @return $this
     */
    public function setSoundDict(string $appSoundFile, bool $isCritical, float $volume): self
    {
        if ($volume < 0) {
            $volume = 0.0;
        }
        if ($volume > 1) {
            $volume = 1.0;
        }

        $this->setApsValue('sound', [
            'name' => $appSoundFile,
            'critical' => (int)$isCritical,
            'volume' => $volume,
        ]);
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