<?php declare(strict_types=1);

namespace LDL\Env\Console;

use LDL\File\Finder\Adapter\Local\Facade\LocalFileFinderFacade;
use LDL\File\Finder\FoundFile;
use LDL\Validators\Chain\AndValidatorChain;
use LDL\Validators\RegexValidator;
use Symfony\Component\Console\Application as SymfonyApplication;

class Console extends SymfonyApplication
{
    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct('<info>[ Env file builder ]</info>', $version);

        $foundFiles = LocalFileFinderFacade::find(
            [(__DIR__.'/Command')],
            true,
            new AndValidatorChain([
                new RegexValidator('/^.*\.php$/')
            ])
        );

        /**
         * @var FoundFile $foundFile
         */
        foreach($foundFiles as $foundFile){
            require $foundFile->getPath();

            $class = get_declared_classes();
            $class = $class[count($class) - 1];

            $this->add(new $class());
        }
    }
}
