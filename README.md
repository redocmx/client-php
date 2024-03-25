
# Converting CFDI to PDF

## redocmx/client-php

The `redocmx/client-php` module is a PHP client for interacting with the [redoc.mx](https://redoc.mx) REST API to convert CFDIs (Comprobante Fiscal Digital por Internet) to PDFs. 

This client simplifies the process of sending XML data and retrieving the converted PDF, along with transaction details and metadata. 

## Requirements

PHP 5.6.0 and later.

## Installation

To install the module, run:

```bash
composer require redocmx/client-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once 'vendor/autoload.php';
```

## Usage

First, import the module and create an instance of the Redoc client. 

You can optionally pass your API key as an argument, or the client will attempt to load it from the REDOC_API_KEY environment variable.

```php
  use Redocmx\RedocmxClient;

  # Create a Redoc instance
  $redoc = new RedocmxClient('your_api_key_here');
```

### Converting CFDI to PDF

The `redocmx/client-php` provides two options for loading CFDI data: from a file or directly from a string.

#### Option 1: Load XML from the File System

```php
$cfdi = $redoc->cfdi()->fromFile('./path/to/your/file.xml');
```

#### Option 2: Use an XML Content String

```php
$cfdi = $redoc->cfdi()->fromString('<xml_content_string_here>');
```

### Generating the PDF

To convert the loaded CFDI to a PDF:

```php
  try {
    # Create the PDF
    $pdf = $cfdi->toPdf();

    echo("Transaction ID: " . $pdf->getTransactionId() . "\n");
    echo("Total Pages: " . $pdf->getTotalPages() . "\n");
    echo("Total Time: " .$pdf->getTotalTimeMs() . "\n");
    print_r($pdf->getMetadata());

    # Save in file system
    file_put_contents('./result.pdf', $pdf->toBuffer());

  } catch (Exception $e) {
    echo "An error occurred during the conversion: " . $e->getMessage();
  }
```

## Examples

- [Basic example](https://github.com/redocmx/cfdi-a-pdf-ejemplos)
- [Custom logo and colors](https://github.com/redocmx/cfdi-a-pdf-ejemplos)
- [Change language to English](https://github.com/redocmx/cfdi-a-pdf-ejemplos)
- [Add additional rich content](https://github.com/redocmx/cfdi-a-pdf-ejemplos)

## API Reference

### RedocmxClient

The `$redoc` object is an instance of `RedocmxClient`, created using `new RedocmxClient(api_key)`.

| Method     | Description |
| -------- | ------- |
| $redoc->cfdi()->**fromFile(filePath)**  |  Returns: **Cfdi** - **Instance**<br>Loads file content from the file system for converting a CFDI to PDF. The file should be valid XML for a CFDI.<br>It returns an instance of the Cfdi class, which can be used to obtain the PDF.|
| $redoc->cfdi()->**fromString(fileContent)**  |  Returns: **Cfdi** - **Instance**<br>Uses a CFDI as a string for converting the CFDI to PDF. The string should be valid XML for a CFDI.<br>It returns an instance of the Cfdi class, which can be used to obtain the PDF.|

### Cfdi

The `$cfdi` object is an instance of `Cfdi`, created using `$redoc->cfdi()->fromFile(filePath)` or `$redoc->cfdi()->fromString(fileContent)`.

| Method     | Description |
| -------- | ------- |
| $cfdi()->**setAddenda(str)**  |  Params: **String**<br>Allows the use of a [redoc addenda](https://redoc.mx/docs/addenda) for full control over the design of the final PDF.|
| $cfdi()->**toPdf(options)**  |  Params: **Object** - [PdfOptions](#pdfoptions)<br>Returns: **Pdf** - **Instance**<br>An instance of the Pdf class, which, when invoked, converts the CFDI into a PDF and stores it, along with the generated data from the conversion request.|

##### PdfOptions
```php
[
	"style_pdf"=>"John"
]
```
### Pdf

The `$pdf` object is an instance of `Pdf`, created from `$cfdi->toPdf(options)`.

| Method     | Description |
| -------- | ------- |
| $pdf->**toBuffer()**  |  Returns: **Buffer**<br>The PDF document as a buffer, ready for storage in the file system or to be sent back in an HTTP request.|
| $pdf->**getTransactionId()**  |  Returns: **String - UUID**<br>A unique ID for the transaction request to the redoc service.|
| $pdf->**getTotalPages()** | Returns: **Integer**<br>The total number of pages generated for the PDF file. |
| $pdf->**getTotalTimeMs()**    | Returns: **Integer**<br>Time in milliseconds taken to convert the CFDI to PDF. |
| $pdf->**getMetadata()**    | Returns: **Object** - [CfdiMetadata]()<br>General information from the converted CFDI. |

##### CfdiMetadata
```php 
[
    TDB...
]
```

## Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue for any bugs, features, or improvements.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.