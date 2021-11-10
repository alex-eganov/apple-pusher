<?php

namespace bIbI4k0\ApplePusher\Payload;

/**
 * Class AlertPayload
 * @package bIbI4k0\ApplePusher\Payload
 */
class AlertPayload extends AbstractPayload
{
    /**
     * @var bool
     */
    private bool $alertAsString = false;

    /**
     * @var string|null
     */
    private ?string $title;

    /**
     * @var string|null
     */
    private ?string $subTitle;

    /**
     * @var string|null
     */
    private ?string $body;

    /**
     * @param string|null $title
     * @param string|null $subTitle
     * @param string|null $body
     */
    public function __construct(string $title = null, string $subTitle = null, string $body = null)
    {
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE_ALERT;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Sets the flag, that a string should be used as the value of "alert" field of the payload.
     * Will use "title" for value
     *
     * @param bool $flag
     */
    public function setAlertAsString(bool $flag): void
    {
        $this->alertAsString = $flag;
    }

    /**
     * @return array
     */
    public function getApsData(): array
    {
        if ($this->alertAsString) {
            $this->setApsValue('alert', $this->title);
            return parent::getApsData();
        }

        $alertData = [
            'title' => $this->getTitle()
        ];
        if ($this->subTitle !== null) {
            $alertData['subtitle'] = $this->getSubTitle();
        }
        if ($this->body !== null) {
            $alertData['body'] = $this->getBody();
        }

        $this->setApsValue('alert', $alertData);

        return parent::getApsData();
    }
}
