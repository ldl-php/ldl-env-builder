<?php declare(strict_types=1);

namespace LDL\Env\Builder\Config\Factory;

use LDL\Env\Builder\Config\EnvBuilderConfig;
use LDL\Env\Builder\Config\EnvBuilderConfigInterface;
use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\Env\Util\File\Parser\Options\EnvFileParserOptions;
use LDL\Env\Util\File\Writer\EnvFileWriter;
use LDL\Env\Util\File\Writer\Options\EnvFileWriterOptions;
use LDL\Env\Util\Line\Collection\Compiler\EnvCompiler;
use LDL\Env\Util\Line\Collection\Compiler\Options\EnvCompilerOptions;

class EnvBuilderConfigFactory
{

    public static function fromFile(string $file, bool $resetGenerationParameters=true) : EnvBuilderConfigInterface
    {
        $file = json_decode(file_get_contents($file), true, 2048, \JSON_THROW_ON_ERROR);

        /**
         * Unset generation parameters so they are updated when the next build takes place
         */
        if($resetGenerationParameters && array_key_exists('generation', $file)){
            unset($file['generation']);
        }

        /**
         * Unset the user
         */

        return self::fromArray($file);
    }

    public static function fromArray(array $data) : EnvBuilderConfigInterface
    {
        return new EnvBuilderConfig(
            new EnvFileParser(
                EnvFileParserOptions::fromArray(array_key_exists('reader', $data) ? $data['reader'] : [])
            ),
            new EnvFileFinder(
                EnvFileFinderOptions::fromArray(array_key_exists('finder', $data) ? $data['finder'] : [])
            ),
            new EnvCompiler(
                EnvCompilerOptions::fromArray(array_key_exists('compiler', $data) ? $data['compiler'] : [])
            ),
            new EnvFileWriter(
                EnvFileWriterOptions::fromArray(array_key_exists('writer', $data) ? $data['writer'] : [])
            )
        );
    }

}