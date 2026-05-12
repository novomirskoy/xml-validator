<?php

declare(strict_types=1);

namespace Tests\Novomirskoy\XmlValidator;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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

    protected function setUp(): void
    {
        $this->sut = new Validator();
    }

    #[Test]
    #[DataProvider('validXmlProvider')]
    public function validateValidXmlWithSchemaFromFile(string $xml, Schema $schema): void
    {
        // Act
        $result = $this->sut->validate(xml: $xml, schema: $schema);

        // Assert
        $this->assertTrue($result->isValid());
    }

    public static function validXmlProvider(): iterable
    {
        $phpunitXml = <<<'XML'
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

        $phpbuXml = <<<'XML'
            <phpbu xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                   xsi:noNamespaceSchemaLocation="https://www.phpbu.de/schema/6.0/phpbu.xsd"
                   verbose="true">

              <logging>
                <log type="json" target="/tmp/logfile.json"/>
              </logging>

              <backups>
                <backup name="myAppDB">
                  <!-- data to backup -->
                  <source type="mysql">
                    <option name="databases" value="dbname"/>
                    <option name="tables" value=""/>
                    <option name="ignoreTables" value=""/>
                    <option name="structureOnly" value="dbname.table1,dbname.table2"/>
                  </source>

                  <!-- where should the backup be stored -->
                  <target dirname="/tmp/backup" filename="mysqldump-%Y%m%d-%H%i.sql" compress="bzip2"/>

                  <!-- do some sanity checks to make sure everything worked as planned -->
                  <check type="sizemin" value="2M"/>

                  <!-- sync backup to some location or service -->
                  <sync type="sftp">
                    <option name="host" value="example.com"/>
                    <option name="user" value="user.name"/>
                    <option name="password" value="topsecret"/>
                    <option name="path" value="some/dir"/>
                  </sync>

                  <!-- deletes old backups -->
                  <cleanup type="capacity">
                    <option name="size" value="100M"/>
                  </cleanup>
                </backup>
              </backups>
            </phpbu>
        XML;

        yield 'validate phpunit xml with schema from file' => [
            'xml' => $phpunitXml,
            'schema' => Schema::file(__DIR__ . '/data/xsd/phpunit.xsd'),
        ];

        yield 'validate phpunit xml with schema from string' => [
            'xml' => $phpunitXml,
            'schema' => Schema::string(file_get_contents(__DIR__ . '/data/xsd/phpunit.xsd')),
        ];

        yield 'validate phpbu xml with schema from file' => [
            'xml' => $phpbuXml,
            'schema' => Schema::file(__DIR__ . '/data/xsd/phpbu.xsd'),
        ];

        yield 'validate phpbu xml with schema from string' => [
            'xml' => $phpbuXml,
            'schema' => Schema::string(file_get_contents(__DIR__ . '/data/xsd/phpbu.xsd')),
        ];
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
        $result = $validator->validate(xml: $xml, schema: Schema::file(__DIR__ . '/data/xsd/phpunit.xsd'));

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
        $validator->validate(xml: $xml, schema: Schema::file(__DIR__ . '/data/xsd/phpunit.xsd'));
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
        $validator->validate(xml: $xml, schema: Schema::file(__DIR__ . '/data/xsd/phpunit.xsd'));
    }

    #[Test]
    public function validateXmlWithoutSchema(): void
    {
        // Arrange
        $validator = $this->sut;
        $xml = '<foo></foo>';

        // Act
        $result = $validator->validate(xml: $xml);

        // Assert
        $this->assertTrue($result->isValid());
    }
}
