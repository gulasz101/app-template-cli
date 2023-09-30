<?php

declare(strict_types=1);

namespace CLI;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(name: 'say:hello-from-external-server')]
class SayHelloFromExternalServerCommand extends Command
{
    public function __construct(readonly string $externalUrl)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = HttpClient::create();

        $output->writeln(
            $client
                ->request(
                    method: 'GET',
                    url: $this->externalUrl,
                )
                ->getContent(),
        );

        return Command::SUCCESS;
    }
}
