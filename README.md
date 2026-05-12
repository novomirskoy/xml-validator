# Xml validator

XML document validation using XSD schemas

## Installation

Install the library using Composer:

```bash
composer require novomirskoy/xml-validator
```

### Requirements

The following PHP extensions are required for the library to work:
- `ext-dom`
- `ext-libxml`

## Usage

Create an instance of the `Validator` class and call the `validate()` method,
passing an XML document as a string and a schema. The schema can be either a **string** or a **path to an XSD file**.
Create a schema instance using the named constructors `Schema::file()`
or `Schema::string()`, passing the file path or schema string respectively.

The validator does not store the validation result, so it immediately returns a Result object that indicates
whether the validation was successful and returns a list of all errors that occurred.

## Examples

```php
// Example XML document
$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<book>
    <title>Design Patterns</title>
    <author>Gamma</author>
    <year>1994</year>
</book>
XML;

// Validation using XSD schema from string
$xsdSchema = <<<XSD
<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xs:element name="book">
        <xs:complexType>
            <xs:sequence>
                <xs:element name="title" type="xs:string"/>
                <xs:element name="author" type="xs:string"/>
                <xs:element name="year" type="xs:integer"/>
            </xs:sequence>
        </xs:complexType>
    </xs:element>
</xs:schema>
XSD;

$result = new Validator()->validate(
    xml: $xml,
    schema: Schema::string($xsdSchema),
);

if (!$result->isValid()) {
    foreach ($result->errors as $error) {
        echo $error, PHP_EOL;
    }
}

// Validation using XSD schema from file
$result = new Validator()->validate(
    xml: $xml,
    schema: Schema::file('/path/to/schema.xsd'),
);

if (!$result->isValid()) {
    foreach ($result->errors as $error) {
        echo $error, PHP_EOL;
    }
}
```

## Documentation

For the full documentation in Russian, please see [docs/README.ru.md](docs/README.ru.md).
