<?php

declare(strict_types=1);

namespace Novomirskoy\XmlValidator;

use InvalidArgumentException;

use function sprintf;

final class Schema
{
    public private(set) ?string $file = null;
    public private(set) ?string $string = null;

    /**
     * @param non-empty-string $file
     */
    public static function file(string $file): self
    {
        if (!is_file($file)) {
            throw new InvalidArgumentException(sprintf('По пути "%s" нет файла', $file));
        }

        $schema = new self();
        $schema->file = $file;

        return $schema;
    }

    /**
     * @param non-empty-string $string
     */
    public static function string(string $string): self
    {
        $schema = new self();
        $schema->string = $string;

        return $schema;
    }
}
