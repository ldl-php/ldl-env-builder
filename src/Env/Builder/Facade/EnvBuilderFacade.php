<?php declare(strict_types=1);

namespace LDL\Env\Builder\Facade;

use LDL\Env\File\Finder\Facade\EnvFileFinderFacade;
use LDL\Env\Util\Compiler\EnvCompiler;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;

final class EnvBuilderFacade implements EnvBuilderFacadeInterface
{
    public static function build(
        iterable $directories,
        iterable $excludeDirectories=null,
        iterable $excludeFiles=null
    ) : EnvLineCollectionInterface
    {
        return (new EnvCompiler())->compile(
            (new EnvFileParser(null,null,null))->parse(
                EnvFileFinderFacade::find($directories, $excludeDirectories, $excludeFiles)
            )
        );
    }
}