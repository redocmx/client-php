<?php

namespace Redocmx;

require_once 'Pdf.php';

class Cfdi
{
    private $pdf = null;
    private $addenda = null;
    private $filePath = null;
    private $fileContent = null;
    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function fromFile($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function fromString($fileContent)
    {
        $this->fileContent = $fileContent;
        return $this;
    }

    private function getXmlContent()
    {
        if ($this->fileContent) {
            return ['content' => $this->fileContent, 'type' => 'string'];
        }

        if ($this->filePath) {
            if (!file_exists($this->filePath)) {
                throw new \Exception("Failed to read XML content from file: {$this->filePath}. The file does not exist.");
            }

            if(!is_readable($this->filePath)){
                throw new \Exception("Permission denied: {$this->filePath}. The file exists but cannot be read.");
            }

            $this->fileContent = file_get_contents($this->filePath);
            return ['content' => $this->fileContent, 'type' => 'string'];
        }

        throw new \Exception('XML content for the CFDI must be provided.');
    }

    public function setAddenda($addenda)
    {
        if (!is_string($addenda)) {
            throw new \InvalidArgumentException('setAddenda function only accepts a string parameter.');
        }

        $this->addenda = $addenda;
    }

    public function toPdf($payload = [])
    {
        if ($this->pdf) {
            return $this->pdf;
        }

        if (!is_array($payload)) {
            throw new \InvalidArgumentException('toPdf function only accepts an array as a parameter.');
        }

        $file = $this->getXmlContent();
        $payload['format'] = 'pdf';
        
        if ($this->addenda) {
            $payload['addenda'] = $this->addenda;
        }
        
        $result = $this->service->cfdisConvert($file, $payload);
        $this->pdf = new Pdf($result);
        
        return $this->pdf;
    }
}
