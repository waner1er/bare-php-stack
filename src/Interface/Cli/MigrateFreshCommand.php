<?php

declare(strict_types=1);

namespace App\Interface\Cli;

use App\Interface\Cli\Contract\CommandInterface;
use App\Interface\Cli\Contract\OutputInterface;

class MigrateFreshCommand implements CommandInterface
{
    public function __construct(
        private OutputInterface $output,
        private DatabaseDropCommand $drop,
        private DatabaseCreateCommand $create,
        private MigrateCommand $migrate,
        private SeedCommand $seed,
    ) {}

    public function execute(?string $name, array $options = []): void
    {
        $withSeed = in_array('--seed', $options, true);

        $this->output->writeln("→ Drop");
        $this->drop->execute(null, ['--force']);

        $this->output->writeln("\n→ Create");
        $this->create->execute(null);

        $this->output->writeln("\n→ Migrate");
        $this->migrate->execute(null);

        if ($withSeed) {
            $this->output->writeln("\n→ Seed");
            $this->seed->execute(null);
        }

        $this->output->writeln("\n✓ migrate:fresh terminé." . ($withSeed ? ' (avec seed)' : ''));
    }
}
