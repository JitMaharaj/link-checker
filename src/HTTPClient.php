<?php

namespace LinkChecker;

use InvalidArgumentException;

class HTTPClient
{
    const TYPE_GET = 'GET';
    const TYPE_POST = 'POST';
    const TYPE_PUT = 'PUT';
    const TYPE_DELETE = 'DELETE';
    const TYPE_PATCH = 'PATCH';
    const TYPE_OPTIONS = 'OPTIONS';
    const TYPE_HEAD = 'HEAD';

    const ALLOWES_METHOD_TYPES = [
        self::TYPE_GET,
        self::TYPE_POST,
        self::TYPE_PUT,
        self::TYPE_DELETE,
        self::TYPE_PATCH,
        self::TYPE_OPTIONS,
        self::TYPE_HEAD,
    ];

    /**
     * @param string $url               url of the endpoint
     * @param int $type                 url of the endpoint
     * @param mixed $payload            payload to send in the body request
     * @param bool $verifyCertificate   if false it will not verify the SSL certificate
     * @param bool $verbose             if true it will print cURL details
     * @return array                    response in the format [data, headers, statusCode]
     */
    public static function request(
        string $url,
        string $type = self::TYPE_GET,
        mixed $payload = null,
        bool $verifyCertificate = true,
        bool $verbose = false,
    ): array {
        $type = strtoupper($type);
        if (!in_array($type, self::ALLOWES_METHOD_TYPES, true)) {
            throw new InvalidArgumentException('Invalid method type ' . $type);
        }

        $ch = curl_init($url);

        $requestHeaders = [
            "Content-Type: application/json",
            "Accept: */*",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36",
            "Connection: keep-alive",
        ];

        if ($type !== self::TYPE_GET) {
            if ($payload) {
                $bodyContent = json_encode($payload);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyContent);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyContent);
                $requestHeaders[] = "Content-Length: " . strlen($bodyContent);
            }
            if ($type === self::TYPE_POST) {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $verbose);

        if (!$verifyCertificate) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_setopt(
            $ch,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$responseHeaders) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2)
                    return $len;

                $responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            }
        );

        $responseHeaders = [];
        $responseData = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'result' => $responseData,
            'headers' => $responseHeaders,
            'statusCode' => $statusCode
        ];
    }
}
