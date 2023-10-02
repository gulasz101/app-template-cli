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
class ExternalServerIsSayingHelloTest extends TestCase
{
    public function testExternalServerIsSayingHello(): void
    {
        //
        // arrange
        //

        $externalApi = 'http://localhost:8899';
        $externalApiContentType = 'application/json';
        $externalApiBody = <<<'BODY'
            {
              "saying": "hello"
            }
            BODY;

        $mockServer = new Process(
            command: [
                'php',
                '-S',
                '0.0.0.0:8899',
                __DIR__.'/mock_router.php',
            ],
            env: [
                'API_CONTENT_TYPE' => $externalApiContentType,
                'API_CONTENT_BODY' => $externalApiBody,
            ]
        );
        $mockServer->start();
        try {
            //
            // act
            //

            $process = new Process(
                command: [
                    'php',
                    __DIR__.'/../console.php',
                    'say:hello-from-external-server',
                ],
                env: [
                    'CLI_EXTERNAL_API' => $externalApi,
                    'CLI_DB_DSN' => 'sqlite::memory:',
                ],
            );
            $process->mustRun();

            //
            // assert
            //

            $this->assertSame(
                expected: $process->getOutput(),
                actual: $externalApiBody.PHP_EOL,
            );
        } finally {
            $mockServer->stop();
        }
    }
}
