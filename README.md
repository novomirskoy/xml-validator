# Xml validator

Валидация XML документов с помощью XSD схем

## Установка

Установите библиотеку с помощью Composer:

```bash
composer require novomirskoy/xml-validator
```

### Требования

Для работы библиотеки необходимы следующие PHP расширения:
- `ext-dom`
- `ext-libxml`

## Использование

Необходимо создать экземпляр класса `Validator` и вызвать метод `validate()`,
передав в него XML-документ в виде строки и схему. Схема может быть как **строкой**, так и **путём к XSD-файлу**.
Создать экземпляр схемы можно с помощью именованных конструкторов `Schema::file()`
или `Schema::string()`, передавая туда путь к файлу или строку со схемой соответственно.

Валидатор не хранит результат валидации, поэтому сразу возвращает объект типа Result, который сообщает,
была ли валидация успешной, а также возвращает список всех возникших ошибок.

## Как пользоваться

```php
// Пример XML-документа
$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<book>
    <title>Паттерны проектирования</title>
    <author>Гамма</author>
    <year>1994</year>
</book>
XML;

// Валидация с использованием XSD схемы из строки
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

// Валидация с использованием XSD схемы из файла
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
