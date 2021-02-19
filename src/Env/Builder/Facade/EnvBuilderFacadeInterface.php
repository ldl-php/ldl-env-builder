<?php declare(strict_types=1);

namespace LDL\Env\Builder\Facade;

use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;

interface EnvBuilderFacadeInterface
{
    public static function build(
        iterable $directories,
        iterable $excludeDirectories=null,
        iterable $excludeFiles=null
    ) : EnvLineCollectionInterface;
}