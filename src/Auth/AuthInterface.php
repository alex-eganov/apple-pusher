<?php

namespace bIbI4k0\ApplePusher\Auth;

/**
 * Interface AuthInterface
 * @package ApplePusher\Auth
 */
interface AuthInterface
{
    /**
     * @return array
     */
    public function getCurlOptions(): array;

    /**
     * @return array
     */
    public function getRequestHeaders(): array;
}
