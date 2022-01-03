<?php

declare(strict_types=1);

namespace LDL\Env\Console\Command;

use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PrintFilesCommand extends SymfonyCommand
{
    public const COMMAND_NAME = 'env:print';

    public function configure(): void
    {
        $defaults = EnvFileFinderOptions::fromArray([]);

        $defaultDirectories = implode(', ', $defaults->getDirectories());
        $defaultFiles = implode(', ', $defaults->getFiles());

        $this->setName(self::COMMAND_NAME)
            ->setDescription('Prints .env files')
            ->addArgument(
                'scan-directories',
                InputArgument::REQUIRED,
                sprintf(
                    'Comma separated list of directories to scan, default: %s',
                    $defaultDirectories
                )
            )
            ->addOption(
                'scan-files',
                'l',
                InputOption::VALUE_REQUIRED,
                sprintf(
                    'Comma separated list of files to scan, default: %s',
                    $defaultFiles
                ),
                $defaultFiles
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $total = 0;

            $output->writeln("<info>[ Env files list ]</info>\n");

            $finderOptions = EnvFileFinderOptions::fromArray([
                'directories' => explode(',', $input->getArgument('scan-directories')),
                'files' => explode(',', $input->getOption('scan-files')),
            ]);

            $finder = new EnvFileFinder($finderOptions);

            $files = $finder->find();

            foreach ($files as $file) {
                $total++;
                $output->writeln($file);
            }

            $output->writeln("\n<info>Total files found: $total</info>");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");

            return self::FAILURE;
        }
    }
}
