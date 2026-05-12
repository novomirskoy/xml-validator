<?php

declare(strict_types=1);

namespace Novomirskoy\XmlValidator;

use LibXMLError;
use Stringable;

use function sprintf;
use function trim;

use const LIBXML_ERR_WARNING;

final readonly class Error implements Stringable
{
    private function __construct(
        public string $level = '',
        public int $code = 0,
        public string $message = '',
        public string $file = '',
        public int $line = 0,
        public int $column = 0,
    ) {}

    public static function fromLibXMLError(LibXMLError $error): self
    {
        return new self(
            level: LIBXML_ERR_WARNING === $error->level ? 'WARNING' : 'ERROR',
            code: $error->code,
            message: trim($error->message),
            file: $error->file ?? 'n/a',
            line: $error->line,
            column: $error->column,
        );
    }

    public static function fromEmptyError(): self
    {
        return new self(message: 'XML невалиден');
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s %s] %s (in %s - line %d, column %d)',
            $this->level,
            $this->code,
            $this->message,
            $this->file,
            $this->line,
            $this->column,
        );
    }
}
