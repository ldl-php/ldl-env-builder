<?php declare(strict_types=1);

namespace LDL\Env\File\Finder\Facade;

use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use LDL\File\Collection\ReadableFileCollection;

final class EnvFileFinderFacade
{

    /**
     * @param iterable $directories
     * @param iterable|null $excludeDirectories
     * @param iterable|null $excludedFiles
     * @return ReadableFileCollection
     * @throws \LDL\Env\File\Finder\Exception\NoFilesFoundException
     */
    public static function find(
        iterable $directories,
        iterable $excludeDirectories=null,
        iterable $excludedFiles=null
    ) : ReadableFileCollection
    {
        return (new EnvFileFinder(
            EnvFileFinderOptions::fromArray([
                'files' => ['.env'],
                'directories' => $directories,
                'excludedDirectories' => $excludeDirectories,
                'excludeFiles' => $excludedFiles
            ])))->find();

    }

}
