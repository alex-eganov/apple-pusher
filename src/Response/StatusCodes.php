<?php

namespace bIbI4k0\ApplePusher\Response;

/**
 * APNS error codes
 *
 * Class ResponseStatus
 * @package bIbI4k0\ApplePusher
 */
class StatusCodes
{
    public const OK = 200;

    public const BAD_REQUEST = 400;
    public const AUTH_ERR = 403;
    public const METHOD_INVALID = 405;
    public const DEVICE_TOKEN_INVALID = 410;
    public const PAYLOAD_TOO_LARGE = 413;
    public const TOO_MANY_REQUESTS_SAME_DEVICE = 429;
    public const SERVER_ERR = 500;
    public const SERVER_UNAVAILABLE = 503;
}
