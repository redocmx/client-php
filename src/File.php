<?php

namespace Redocmx;

class File
{
  private $filePath;
  private $fileBuffer;
  private $fileContent;

  public function __construct()
  {
    $this->filePath = null;
    $this->fileBuffer = null;
    $this->fileContent = null;
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

  public function getFile()
  {
    if (!empty($this->fileContent)) {
      return ['content' => $this->fileContent, 'type' => 'string'];
    }

    if (!empty($this->fileBuffer)) {
      return ['content' => $this->fileBuffer, 'type' => 'buffer'];
    }

    if (!empty($this->filePath)) {
      if (!file_exists($this->filePath)) {
        throw new \Exception("Failed to read XML content from file: {$this->filePath}. The file does not exist.");
      }

      if (!is_readable($this->filePath)) {
        throw new \Exception("Permission denied: {$this->filePath}. The file exists but cannot be read.");
      }

      $this->fileBuffer = file_get_contents($this->filePath);
      return ['content' => $this->fileBuffer, 'type' => 'buffer'];
    }

    throw new \Exception("Failed to load file " . get_class($this) . ", you must use fromFile or fromString.");
  }
}
