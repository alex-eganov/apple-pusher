<?php

namespace ApplePusher;


/**
 * APNS error codes
 *
 * Class ResponseStatus
 * @package ApplePusher
 */
class ResponseStatus
{
    public const STATUS_OK = 200;

    public const STATUS_BAD_REQUEST = 400;
    public const STATUS_AUTH_ERR = 403;
    public const STATUS_METHOD_INVALID = 405;
    public const STATUS_DEVICE_TOKEN_INVALID = 410;
    public const STATUS_PAYLOAD_TOO_LARGE = 413;
    public const STATUS_TOO_MANY_REQUESTS_SAME_DEVICE = 429;
    public const STATUS_SERVER_ERR = 500;
    public const STATUS_SERVER_UNAVAILABLE = 503;
}