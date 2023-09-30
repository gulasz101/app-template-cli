<?php

declare(strict_types=1);

use CLI\SayHelloCommand;
use CLI\SayHelloFromExternalServerCommand;
use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$app = new Application();

$app->add(new SayHelloCommand());
$app->add(new SayHelloFromExternalServerCommand(
    getenv('CLI_EXTERNAL_API') ?: throw new RuntimeException('CLI_EXTERNAL_API env is missing'),
));

$app->run();
