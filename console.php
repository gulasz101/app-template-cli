<?php

declare(strict_types=1);

use CLI\SayHalloFromDBCommand;
use CLI\SayHelloCommand;
use CLI\SayHelloFromExternalServerCommand;
use gulasz101\Type;
use Symfony\Component\Console\Application;

use function gulasz101\gettype;

require __DIR__.'/vendor/autoload.php';

$app = new Application();

//
// TODO: bootstrap, should be handle... geantlier xD
//
$app->add(new SayHelloCommand());
$app->add(new SayHelloFromExternalServerCommand(
    match (gettype($apiUrl = getenv('CLI_EXTERNAL_API'))) {
        Type::BOOLEAN => throw new RuntimeException('CLI_EXTERNAL_API env is missing'),
        Type::STRING => $apiUrl,
        default => throw new RuntimeException('CLI_EXTERNAL_API env is setup with wrong type'),
    }
));
$app->add(new SayHalloFromDBCommand(
    new PDO(
        match (gettype($dsn = getenv('CLI_DB_DSN'))) {
            Type::BOOLEAN => throw new RuntimeException('CLI_DB_DSN env is missing'),
            Type::STRING => $dsn,
            default => throw new RuntimeException('CLI_DB_DSN env is setup with wrong type'),
        }
    ))
);

$app->run();
