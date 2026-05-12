<?php

declare(strict_types=1);

namespace Tests\Novomirskoy\XmlValidator;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Novomirskoy\XmlValidator\Schema;
use Novomirskoy\XmlValidator\Validator;
use RuntimeException;

use function file_get_contents;
use function sprintf;

#[CoversClass(Validator::class)]
#[CoversClass(Schema::class)]
final class ValidatorTest extends TestCase
{
    private Validator $sut;
    /** @var non-empty-string */
    private string $schemaPath;
    /** @var non-empty-string */
    private string $schemaContent;

    protected function setUp(): void
    {
        $this->sut = new Validator();
        $this->schemaPath = __DIR__ . '/data/xsd/phpunit.xsd';
        $schemaContent = file_get_contents($this->schemaPath);
        if (false === $schemaContent || '' === $schemaContent) {
            throw new RuntimeException(sprintf('Схема по пути %s отсутствует или является пустым файлом', $this->schemaPath));
        }
        $this->schemaContent = $schemaContent;
    }

    #[Test]
    public function validateValidXmlWithSchemaFromFile(): void
    {
        // Arrange
        $validator = $this->sut;

        // Act
        $xml = <<<'XML'
            <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:noNamespaceSchemaLocation="https://phpunit.de"
                     bootstrap="vendor/autoload.php"
                     cacheDirectory=".phpunit.cache"
                     requireCoverageMetadata="true"
                     colors="true">

                <testsuites>
                    <testsuite name="Default">
                        <directory>tests</directory>
                    </testsuite>
                </testsuites>

                <source>
                    <include>
                        <directory>src</directory>
                    </include>
                </source>
            </phpunit>
        XML;
        $result = $validator->validate(xml: $xml, schema: Schema::file($this->schemaPath));

        // Assert
        $this->assertTrue($result->isValid());
    }

    #[Test]
    public function validateValidXmlWithSchemaFromString(): void
    {
        // Arrange
        $validator = $this->sut;

        // Act
        $xml = <<<'XML'
            <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:noNamespaceSchemaLocation="https://phpunit.de"
                     bootstrap="vendor/autoload.php"
                     cacheDirectory=".phpunit.cache"
                     requireCoverageMetadata="true"
                     colors="true">

                <testsuites>
                    <testsuite name="Default">
                        <directory>tests</directory>
                    </testsuite>
                </testsuites>

                <source>
                    <include>
                        <directory>src</directory>
                    </include>
                </source>
            </phpunit>
        XML;
        $result = $validator->validate(xml: $xml, schema: Schema::string($this->schemaContent));

        // Assert
        $this->assertTrue($result->isValid());
    }

    #[Test]
    public function validateInvalidXml(): void
    {
        // Arrange
        $validator = $this->sut;

        // Act
        $xml = <<<'XML'
            <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                     xsi:noNamespaceSchemaLocation="https://phpunit.de"
                     bootstrap="vendor/autoload.php"
                     cacheDirectory=".phpunit.cache"
                     requireCoverageMetadata="true"
                     colors="true">

                <teztsuites>
                    <testsuite name="Default">
                        <directory>tests</directory>
                    </testsuite>
                </teztsuites>
            </phpunit>
        XML;
        $result = $validator->validate(xml: $xml, schema: Schema::file($this->schemaPath));

        // Assert
        $this->assertFalse($result->isValid());
    }

    #[Test]
    public function validateWithNonExistingSchema(): void
    {
        // Arrange
        $validator = $this->sut;
        $xml = '';

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $validator->validate(xml: $xml, schema: Schema::file($this->schemaPath));
    }

    #[Test]
    public function validateEmptyXml(): void
    {
        // Arrange
        $validator = $this->sut;
        $xml = '';

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $validator->validate(xml: $xml, schema: Schema::file($this->schemaPath));
    }
}
