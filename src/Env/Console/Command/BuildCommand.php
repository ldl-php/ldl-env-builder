<?php declare(strict_types=1);

namespace LDL\Env\Console\Command;

use LDL\Env\Builder\Config\EnvBuilderConfig;
use LDL\Env\Builder\Config\Writer\EnvBuilderConfigWriter;
use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\Env\Util\File\Parser\Options\EnvFileParserOptions;
use LDL\Env\Util\File\Writer\EnvFileWriter;
use LDL\Env\Util\File\Writer\Options\EnvFileWriterOptions;
use LDL\Env\Util\Line\Collection\Compiler\EnvCompiler;
use LDL\Env\Util\Line\Collection\Compiler\Options\EnvCompilerOptions;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use LDL\Env\Builder\EnvBuilder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends SymfonyCommand
{
    public const COMMAND_NAME = 'env:build';

    public function configure() : void
    {
        $readerDefaults = EnvFileParserOptions::fromArray([]);
        $finderDefaults = EnvFileFinderOptions::fromArray([]);

        $this->setName(self::COMMAND_NAME)
            ->setDescription('Build compiled .env file')
            ->addArgument(
                'output-file',
                InputArgument::REQUIRED,
                'Name of the output file'
            )
            ->addOption(
                'force-overwrite',
                'w',
                InputOption::VALUE_NONE,
                'Overwrite output file'
            )
            ->addOption(
                'scan-directories',
                'd',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Comma separated list of directories to scan, default: %s',
                    implode(', ', $finderDefaults->getDirectories())
                ),
                implode(',', $finderDefaults->getDirectories())
            )
            ->addOption(
                'excluded-directories',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Comma separated list of excluded directories to scan'
            )
            ->addOption(
                'scan-files',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Comma separated list of files to scan',
                implode(', ', $finderDefaults->getFiles())
            )
            ->addOption(
                'variable-overwrite',
                'o',
                InputOption::VALUE_NONE,
                'Allow variable overwrite'
            )
            ->addOption(
                'ignore-syntax-error',
                'i',
                InputOption::VALUE_NONE,
                'Ignore syntax error'
            )
            ->addOption(
                'prefix-variable-depth',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Set directory depth to prefix variable name with directory name',
                $readerDefaults->getDirPrefixDepth()
            )
            ->addOption(
                'add-prefix',
                'x',
                InputOption::VALUE_NONE,
                'Set directory prefix variable name (overrides prefix-variable-depth)',
            )
            ->addOption(
                'convert-to-uppercase',
                'u',
                InputOption::VALUE_NONE,
                'Convert variables to uppercase'
            )
            ->addOption(
                'comments-enabled',
                'c',
                InputOption::VALUE_NONE,
                'Comments enable on .env files'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->build($input, $output);
            return 0;
        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
            return 1;
        }
    }

    private function build(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $start = hrtime(true);
        $excludedDirectories = $input->getOption('excluded-directories');

        try{
            $readerOptions = EnvFileParserOptions::fromArray([
                'skipUnreadable' => false,
                'ignoreSyntaxErrors' => $input->getOption('ignore-syntax-error'),
                'dirPrefixDepth' => $input->getOption('prefix-variable-depth'),
            ]);

            $finderOptions = EnvFileFinderOptions::fromArray([
                'directories' => explode(',', $input->getOption('scan-directories')),
                'files' => explode(',', $input->getOption('scan-files')),
                'excludedDirectories' => null !== $excludedDirectories ? explode(',', $excludedDirectories) : [],
            ]);

            $compilerProgress = new ProgressBar($output);
            $compilerProgress->setOverwrite(true);

            $compilerOptions = EnvCompilerOptions::fromArray([
                'allowVariableOverwrite' => $input->getOption('variable-overwrite'),
                'addPrefix' => $input->getOption('add-prefix'),
                'varNameToUpperCase' => $input->getOption('convert-to-uppercase'),
                'commentsEnabled' => $input->getOption('comments-enabled'),
                'onBeforeCompile' => static function($file, $lines) use ($compilerProgress, $output){
                    $output->writeln("\n\n<info>Compiling {$file->getRealPath()}</info>\n");
                    $compilerProgress->setMaxSteps(count($lines));
                },
                'onCompile' => static function() use ($compilerProgress){
                    $compilerProgress->advance();
                },
                'onAfterCompile' => static function() use ($compilerProgress){
                    $compilerProgress->finish();
                }
            ]);

            $writerOptions = EnvFileWriterOptions::fromArray([
                'filename' => $input->getArgument('output-file'),
                'force' => (bool) $input->getOption('force-overwrite')
            ]);

            $title = '[ Building compiled env file ]';

            $output->writeln("\n<info>$title</info>");

            $envFileParser = new EnvFileParser($readerOptions);
            $envFileFinder = new EnvFileFinder($finderOptions);
            $envCompiler = new EnvCompiler($compilerOptions);
            $envFileWriter = new EnvFileWriter($writerOptions);

            $builderConfig = new EnvBuilderConfig(
                $envFileParser,
                $envFileFinder,
                $envCompiler,
                $envFileWriter
            );

            $content = EnvBuilder::build($builderConfig);

            $writerConfig = new EnvBuilderConfigWriter();

            $writerConfig::write($builderConfig, 'env-config.json');
            $envFileWriter->write($content, $writerOptions->getFilename());

            $output->writeln("");

        }catch(\Exception $e) {

            $output->writeln("\n\n<error>Build failed!</error>\n");
            $output->writeln("\n{$e->getMessage()}");

        }

        $end = hrtime(true);
        $total = round((($end - $start) / 1e+6) / 1000,2);

        $output->writeln("\n<info>Took: $total seconds</info>");
    }

}