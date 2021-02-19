<?php declare(strict_types=1);

use LDL\Env\Builder\EnvBuilder;
use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use LDL\Env\Builder\Config\EnvBuilderConfig;
use LDL\Env\Builder\Config\Writer\EnvBuilderConfigWriter;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\Env\Util\File\Parser\Options\EnvFileParserOptions;
use LDL\Env\Util\File\Writer\EnvFileWriter;
use LDL\Env\Util\File\Writer\Options\EnvFileWriterOptions;
use LDL\Env\Util\Line\Collection\Compiler\EnvCompiler;
use LDL\Env\Util\Line\Collection\Compiler\Options\EnvCompilerOptions;

require __DIR__.'/../vendor/autoload.php';

try{
    echo "[ Building compiled env file ]\n";

    $envFileFinder = new EnvFileFinder(EnvFileFinderOptions::fromArray([
        'directories' => [__DIR__.'/Application'],
        'excludedDirectories' => [__DIR__.'/Application/User']
    ]));

    $envFileParser = new EnvFileParser(
        EnvFileParserOptions::fromArray([]),
        new EnvCompiler(EnvCompilerOptions::fromArray([]))
    );

    $envCompiler = new EnvCompiler(EnvCompilerOptions::fromArray([]));

    $envFileWriter = new EnvFileWriter(
        EnvFileWriterOptions::fromArray([
            'filename' => '.env-compiled',
            'force' => true
        ])
    );

    $config = new EnvBuilderConfig(
        $envFileParser,
        $envFileFinder,
        $envCompiler,
        $envFileWriter
    );

    $lines = EnvBuilder::build($config);

    $envFileWriter->write(
        $lines,
        $envFileWriter->getOptions()->getFilename()
    );

    EnvBuilderConfigWriter::write($config, 'env-config.json');

    echo "Build success!\n";

    var_dump($lines->toArray());

}catch(\Exception $e) {

    echo $e->getMessage()."\n";
    echo "[ Build failed! ]\n";
    return;

}

