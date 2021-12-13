<?php

namespace bIbI4k0\ApplePusher\Curl;

use bIbI4k0\ApplePusher\Exception\CurlException;
use CurlHandle;

/**
 * Class CurlWrapper
 * @package bIbI4k0\ApplePusher\Curl
 */
class CurlWrapper implements CurlWrapperInterface
{
    protected ?CurlHandle $handle = null;

    public function __destruct()
    {
        $this->close();
    }

    protected function close(): void
    {
        if ($this->handle !== null) {
            unset($this->handle);
        }
    }

    /**
     * Returns curl handle
     * @return CurlHandle
     * @throws CurlException
     */
    protected function getCurl(): CurlHandle
    {
        $this->close();
        return $this->handle = curl_init() ?: throw new CurlException('Cannot create cURL handle.');
    }

    /**
     * @param array $curlOptions
     * @return CurlResponse
     * @throws CurlException
     */
    final public function send(array $curlOptions): CurlResponse
    {
        $handle = $this->getCurl();
        curl_setopt_array($handle, array_replace($curlOptions, [
            CURLOPT_RETURNTRANSFER => true,
        ]));

        $body = curl_exec($handle);
        if ($body === false) {
            throw new CurlException(
                curl_error($handle),
                curl_errno($handle)
            );
        }

        $code = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

        return new CurlResponse($code, trim($body));
    }
}
