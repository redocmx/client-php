<?php

namespace Redocmx;

class Pdf
{
    private $buffer;
    private $transactionId;
    private $totalPages;
    private $totalTime;
    private $metadata;

    public function __construct($conversionResult)
    {
        $this->buffer = $conversionResult['buffer'];
        $this->transactionId = $conversionResult['transactionId'];
        $this->totalPages = $conversionResult['totalPages'];
        $this->totalTime = $conversionResult['totalTime'];
        $this->metadata = $conversionResult['metadata'];
    }

    public function toBuffer()
    {
        return $this->buffer;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function getTotalPages()
    {
        return $this->totalPages;
    }

    public function getTotalTimeMs()
    {
        return $this->totalTime;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }
}
