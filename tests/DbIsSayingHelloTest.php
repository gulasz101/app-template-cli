<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * @internal
 *
 * @coversNothing
 */
class DbIsSayingHelloTest extends TestCase
{
    private string $testDBPath;
    private string $dbDsn;
    private \PDO $pdo;

    protected function setUp(): void
    {
        $tmpPath = tempnam('/tmp/', 'phpunit.sqlite');

        if (false === $tmpPath) {
            throw new \RuntimeException();
        }

        $this->testDBPath = $tmpPath;
        $this->dbDsn = 'sqlite:'.$tmpPath;

        $this->pdo = new \PDO($this->dbDsn);

        (match ($statement = $this->pdo->prepare(
            <<<'SQL'
                CREATE TABLE data (
                  id SMALLINT AUTO INCREMENT PRIMARY KEY,
                  data TEXT,
                  created_at DATETIME
                )
                SQL
        )) {
            false => throw new \RuntimeException(),
            default => $statement,
        })->execute();
    }

    protected function tearDown(): void
    {
        unlink($this->testDBPath);
    }

    public function testCommandSaysHelloFromDB(): void
    {
        //
        // arrange
        //
        $inputData = [
            'data' => 'random string',
            'created_at' => (new \DateTime())->format(\DateTimeInterface::RFC3339),
        ];

        (match ($statement = $this->pdo->prepare(
            <<<'SQL'
                INSERT INTO data(data, created_at) VALUES (?, ?)
                SQL
        )) {
            false => throw new \RuntimeException(),
            default => $statement,
        }
        )->execute(array_values($inputData));

        //
        // act
        //

        $actualOutput = (new Process(
            command: ['php', __DIR__.'/../console.php', 'say:hello-from-db'],
            env: [
                'CLI_EXTERNAL_API' => 'http://localhost/',
                'CLI_DB_DSN' => $this->dbDsn,
            ],
        ))
            ->mustRun()
            ->getOutput()
        ;

        //
        // assert
        //

        $this->assertSame(
            expected: json_encode([
                $inputData,
            ], JSON_PRETTY_PRINT).PHP_EOL,
            actual: $actualOutput
        );
    }
}
