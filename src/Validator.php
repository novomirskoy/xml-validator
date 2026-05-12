<?php

declare(strict_types=1);

namespace Novomirskoy\XmlValidator;

use DOMDocument;
use InvalidArgumentException;

use function libxml_clear_errors;
use function libxml_use_internal_errors;

use const LIBXML_COMPACT;
use const LIBXML_NONET;
use const XML_DOCUMENT_TYPE_NODE;

final class Validator
{
    public function validate(
        string $xml,
        ?Schema $schema,
    ): Result {
        if ('' === trim(string: $xml)) {
            throw new InvalidArgumentException(message: 'Строка не содержит валидный XML, т.к. является пустой');
        }

        $internalErrors = libxml_use_internal_errors(use_errors: true);
        libxml_clear_errors();

        $dom = new DOMDocument();
        $dom->validateOnParse = true;

        if (!$dom->loadXML(source: $xml, options: LIBXML_NONET | LIBXML_COMPACT)) {
            return Result::invalid(errors: $this->getErrors($internalErrors));
        }

        $dom->normalizeDocument();

        libxml_use_internal_errors($internalErrors);

        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                throw new InvalidArgumentException('Document types are not allowed');
            }
        }

        if (null !== $schema) {
            $internalErrors = libxml_use_internal_errors(use_errors: true);
            libxml_clear_errors();

            $valid = true;

            if ($schema->file) {
                $valid = @$dom->schemaValidate(filename: $schema->file);
            }

            if ($schema->string) {
                $valid = @$dom->schemaValidateSource(source: $schema->string);
            }

            if (!$valid) {
                $errors = $this->getErrors($internalErrors);
                if (count($errors) === 0) {
                    $errors[] = Error::fromEmptyError();
                }

                return Result::invalid(errors: $errors);
            }
        }

        libxml_clear_errors();
        libxml_use_internal_errors(use_errors: $internalErrors);

        return Result::valid();
    }

    /**
     * @return list<Error>
     */
    private function getErrors(bool $internalErrors): array
    {
        $errors = [];

        foreach (libxml_get_errors() as $error) {
            $errors[] = Error::fromLibXMLError(error: $error);
        }

        libxml_clear_errors();
        libxml_use_internal_errors(use_errors: $internalErrors);

        return $errors;
    }
}
