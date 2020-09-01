<?php

namespace bIbI4k0\ApplePusher\Types;

/**
 * Class Alert
 * @package ApplePusher
 */
class Alert implements PayloadInterface
{
    private string $title;
    private ?string $subTitle;
    private ?string $body;

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
     * @return string[]
     */
    public function jsonSerialize(): array
    {
        $data = [
            'title' => $this->getTitle()
        ];
        if ($this->subTitle !== null) {
            $data['subtitle'] = $this->getSubTitle();
        }
        if ($this->body !== null) {
            $data['body'] = $this->getBody();
        }

        return ['alert' => $data];
    }
}
