<?php

namespace Redocmx;

class Cfdi extends File
{
    private $pdf = null;
    private $addenda = null;
    private $addendaReplaceValues = null;
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = Service::getInstance();
    }

    public function setAddenda($addenda, $replaceValues = null)
    {
        if ($addenda && !($addenda instanceof Addenda)) {
            throw new \InvalidArgumentException('Addenda must be Addenda instance.');
        }

        if ($replaceValues && gettype($replaceValues) !== 'array') {
            throw new \InvalidArgumentException('Addenda replace values must be a valid key - value object.');
        }

        $this->addenda = $addenda;
        $this->addendaReplaceValues = $replaceValues;
    }

    public function toPdf($payload = [])
    {
        if ($this->pdf) {
            return $this->pdf;
        }

        if (!is_array($payload)) {
            throw new \InvalidArgumentException('toPdf function only accepts an array as a parameter.');
        }

        $file = $this->getFile();

        if ($this->addenda) {
            $addendaContent = $this->addenda->getFileContent($this->addendaReplaceValues);
            $payload['addenda'] = $addendaContent;
        }

        $payload['format'] = 'pdf';

        $result = $this->service->cfdisConvert($file, $payload);
        $this->pdf = new Pdf($result);

        return $this->pdf;
    }
}
