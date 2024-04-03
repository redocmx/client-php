<?php

namespace Redocmx;

class Addenda extends File
{
  public function __construct()
  {
    parent::__construct();
  }

  public function replaceValues($content, $options = null)
  {
    if (!$options) {
      return $content;
    }

    foreach ($options as $key => $value) {
      $content = str_replace($key, $value, $content);
    }

    return $content;
  }

  public function getFileContent($replaceValues)
  {
    $file = $this->getFile();
    $fileContent = $file['content'];

    return $this->replaceValues($fileContent, $replaceValues);
  }
}
