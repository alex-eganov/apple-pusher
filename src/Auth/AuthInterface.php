<?php

namespace ApplePusher\Auth;

/**
 * Interface AuthInterface
 * @package ApplePusher\Auth
 */
interface AuthInterface
{
    public function getCurlOptions(): array;
}