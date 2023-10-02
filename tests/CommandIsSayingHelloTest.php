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
class CommandIsSayingHelloTest extends TestCase
{
    public function testThatCommandIsSayingHello(): void
    {
        $expectedOutput = 'Hello!'.PHP_EOL;

        $actualOutput = (new Process(
            command: ['php', __DIR__.'/../console.php', 'say:hello'],
            env: [
                'CLI_EXTERNAL_API' => 'http://localhost/',
                'CLI_DB_DSN' => 'sqlite::memory:',
            ],
        ))
            ->mustRun()
            ->getOutput()
        ;

        $this->assertSame($expectedOutput, $actualOutput);
    }
}
