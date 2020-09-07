<?php

namespace bIbI4k0\ApplePusher\Auth;

/**
 * Class CertAuth
 * @package bIbI4k0\ApplePusher\Auth
 */
class CertAuth implements AuthInterface
{
    private string $certFile;
    private string $certPassword;

    /**
     * @param string $certFile
     * @param string $certPassword
     */
    public function __construct(string $certFile, string $certPassword)
    {
        $this->certFile = $certFile;
        $this->certPassword = $certPassword;
    }

    /**
     * @return array
     */
    public function getCurlOptions(): array
    {
        return [
            CURLOPT_SSLCERT => $this->certFile,
            CURLOPT_SSLCERTPASSWD => $this->certPassword,
        ];
    }

    /**
     * @return array
     */
    public function getRequestHeaders(): array
    {
        return [];
    }
}
