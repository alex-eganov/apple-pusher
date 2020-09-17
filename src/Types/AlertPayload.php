<?php

namespace bIbI4k0\ApplePusher\Types;

/**
 * Class Alert
 * @package bIbI4k0\ApplePusher\Types
 */
class AlertPayload extends AbstractPayload
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private  $subTitle;

    /**
     * @var string|null
     */
    private  $body;

    /**
     * @param string $title
     * @param string|null $subTitle
     * @param string|null $body
     */
    public function __construct(string $title, string $subTitle = null, string $body = null)
    {
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->body = $body;
    }

    /**
     * @param string|null $subTitle
     * @return static
     */
    public function setSubTitle(string $subTitle = null): self
    {
        $this->subTitle = $subTitle;
        return $this;
    }

    /**
     * @param string|null $body
     * @return static
     */
    public function setBody(string $body = null): self
    {
        $this->body = $body;
        return $this;
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
     * @return array
     */
    public function getApsData(): array
    {
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
