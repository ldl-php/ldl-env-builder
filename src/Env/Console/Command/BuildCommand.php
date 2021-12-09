<?php declare(strict_types=1);

namespace LDL\Env\Console\Command;

use LDL\Console\Helper\ProgressBarFactory;
use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\Util\Compiler\EnvCompiler;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\File\File;
use LDL\Framework\Base\Collection\CallableCollection;
use LDL\Framework\Helper\IterableHelper;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends SymfonyCommand
{
    public const COMMAND_NAME = 'env:build';

    public function __construct(
        ?string $name = null
    )
    {
        parent::__construct($name ?? self::COMMAND_NAME);
    }

    public function configure() : void
    {
        $this->setDescription('Find, parse and compile env files on directories into a single .env file')
            ->addArgument(
                'output-file',
                InputArgument::REQUIRED,
                'Name of the output file'
            )
            ->addArgument(
                'directories',
                InputArgument::REQUIRED,
                'Directories to scan'
            )
            ->addOption(
                'excluded-directories',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Comma separated list of excluded directories'
            )
            ->addOption(
                'scan-files',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Comma separated list of files to scan',
                '.env'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->build($input, $output);
            return self::SUCCESS;
        }catch(\Exception $e){
            $output->writeln("<error>{$e->getMessage()}</error>");
            return self::FAILURE;
        }
    }

    private function build(
        InputInterface $input,
        OutputInterface $output
    ) : void
    {
        $start = hrtime(true);
        $excludedDirectories = $input->getOption('excluded-directories');
        $verbose = (bool) $input->getOption('verbose');

        try{
            $finderOptions = EnvFileFinderOptions::fromArray([
                'directories' => explode(',', $input->getArgument('directories')),
                'files' => explode(',', $input->getOption('scan-files')),
                'excludedDirectories' => null !== $excludedDirectories ? explode(',', $excludedDirectories) : [],
            ]);

            $compilerProgress = null;

            if(!$verbose) {
                $compilerProgress = new ProgressBar($output);
                $compilerProgress->setBarCharacter("▩");
                $compilerProgress->setEmptyBarCharacter("▢");
                $compilerProgress->setProgressCharacter("▶");
                $compilerProgress->setOverwrite(true);
            }

            $onBeforeParse = new CallableCollection([
                static function($file, $files) use ($output, $compilerProgress){
                    if(null === $compilerProgress){
                        $output->writeln("<fg=white>Parsing file $file ...</>");
                        return;
                    }

                    static $count;

                    if(null === $count){
                        $count = IterableHelper::getCount($files);
                        $compilerProgress->setMaxSteps($count);
                    }

                    $compilerProgress->advance();
                }
            ]);

            $output->writeln("\n<info>[ Building compiled env file ]</info>\n");

            $result = (new EnvCompiler(null,null,))->compile(
                (new EnvFileParser(null,null,null, $onBeforeParse))->parse(
                    (new EnvFileFinder($finderOptions))->find()
                )
            );

            if($compilerProgress) {
                $compilerProgress->finish();
            }

            $file = File::create($input->getArgument('output-file'), (string)$result, 0644, true);

            $output->writeln("\n\n<info>Wrote compiled env file: $file</info>\n");

        }catch(\Exception $e) {

            $output->writeln("\n\n<error>Build failed!</error>\n");
            $output->writeln("\n{$e->getMessage()}");

        }

        $end = hrtime(true);
        $total = round((($end - $start) / 1e+6) / 1000,2);

        $output->writeln("\n<info>Took: $total seconds</info>");
    }

}