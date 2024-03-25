<?php

namespace Redocmx;

require_once 'Service.php';
require_once 'Cfdi.php';

class RedocmxClient
{
    private $apiKey;
    private $service;

    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey ?: getenv('REDOC_API_KEY');
        $this->service = new Service($this->apiKey);
    }

    public function cfdi()
    {
        return new Cfdi($this->service);
    }
}
