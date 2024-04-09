<?php

namespace Redocmx;

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
        return new Cfdi();
    }

    public function addenda()
    {
        return new Addenda();
    }
}
