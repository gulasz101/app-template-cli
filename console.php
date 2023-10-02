<?php

declare(strict_types=1);

use CLI\SayHalloFromDBCommand;
use CLI\SayHelloCommand;
use CLI\SayHelloFromExternalServerCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$app = new Application();

//
// TODO: bootstrap, should be handle... geantlier xD
//
$app->add(new SayHelloCommand());
$app->add(new SayHelloFromExternalServerCommand(
    match ($apiUrl = getenv('CLI_EXTERNAL_API')) {
        false => throw new RuntimeException('CLI_EXTERNAL_API env is missing'),
        default => $apiUrl,
    }
));
$app->add(new SayHalloFromDBCommand(
    new PDO(
        match ($dsn = getenv('CLI_DB_DSN')) {
            false => throw new RuntimeException('CLI_DB_DSN not set'),
            default => $dsn,
        }
    ))
);

$app->run();
