<?php

declare(strict_types=1);

namespace CLI;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'say:hello-from-db')]
class SayHalloFromDBCommand extends Command
{
    public function __construct(protected readonly \PDO $pdo)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = (match ($statement = $this->pdo->query(
            <<<'SQL'
                SELECT data, created_at FROM data
                SQL
        )) {
            false => throw new \RuntimeException(),
            default => $statement,
        })
            ->fetchAll(mode: \PDO::FETCH_ASSOC)
        ;

        $output->writeln(json_encode(value: $data, flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return Command::SUCCESS;
    }
}
