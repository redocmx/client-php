<?php

namespace Redocmx;

class Service
{
    private static $instance = null;
    private $apiKey;
    private $apiUrl;

    function __construct($apiKey)
    {
        $this->apiKey = $apiKey ?: getenv('REDOC_API_KEY');
        $this->apiUrl = getenv('REDOC_API_URL') ?: 'https://api.redoc.mx/cfdis/convert';
    }

    public static function getInstance($apiKey = null)
    {
        if (!self::$instance) {
            self::$instance = new self($apiKey);
        }
        return self::$instance;
    }

    private function parseHeaders($headerString) {
        $headers = array();
        $headerLines = explode("\r\n", $headerString);
        foreach ($headerLines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }

    public function cfdisConvert($file, $payload)
    {
        $boundary = uniqid();
        $eol = "\r\n";
        $delimiter = '-------------' . $boundary;
        $postData = '';

        $postData .= '--' . $delimiter . $eol
            . 'Content-Disposition: form-data; name="xml"; filename="document.xml"' . $eol
            . 'Content-Type: text/xml' . $eol . $eol
            . $file['content'] . $eol;

        if (!empty($payload['style_pdf'])) {
            $postData .= '--' . $delimiter . $eol
                . 'Content-Disposition: form-data; name="style_pdf"' . $eol . $eol
                . $payload['style_pdf'] . $eol;
        }

        if (!empty($payload['addenda'])) {
            $postData .= '--' . $delimiter . $eol
                . 'Content-Disposition: form-data; name="addenda"' . $eol . $eol
                . $payload['addenda'] . $eol;
        }

        $postData .= "--" . $delimiter . "--" . $eol;

        $curl = curl_init($this->apiUrl);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/pdf',
                'Content-Type: multipart/form-data; boundary=' . $delimiter,
                'X-Redoc-Api-Key: ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
        ]);

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        if (curl_errno($curl)) {
            throw new \Exception('Curl error: ' . curl_error($curl));
        }

        $statusCode = $info['http_code'];
        if ($statusCode != 200) {
            throw new \Exception("Request failed with status code $statusCode");
        }

        $headerSize = $info['header_size'];
        $headersString = substr($response, 0, $headerSize);
        $headers = $this->parseHeaders($headersString);

        $body = substr($response, $info['header_size']);

        curl_close($curl);

        $metadata = json_decode(base64_decode($headers['x-redoc-xml-metadata']));
        $transactionId = $headers['x-redoc-transaction-id'];
        $totalPages = $headers['x-redoc-pdf-total-pages'];
        $totalTime = $headers['x-redoc-process-total-time'];

        return [
            'buffer' => $body,
            'metadata' => $metadata,
            'transactionId' => $transactionId,
            'totalPages' => $totalPages,
            'totalTime' => $totalTime
        ];
    }
    
}
