<?php

declare(strict_types=1);

namespace Novomirskoy\XmlValidator;

use Stringable;

use function json_encode;

use const JSON_THROW_ON_ERROR;

final readonly class Result implements Stringable
{
    /**
     * @param list<Error> $errors
     */
    private function __construct(
        public array $errors = [],
    ) {}

    public static function valid(): self
    {
        return new self();
    }

    /**
     * @param list<Error> $errors
     */
    public static function invalid(array $errors): self
    {
        return new self($errors);
    }

    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    public function __toString(): string
    {
        return json_encode(value: $this->errors, flags: JSON_THROW_ON_ERROR);
    }
}
