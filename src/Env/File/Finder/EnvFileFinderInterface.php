<?php declare(strict_types=1);

namespace LDL\Env\File\Finder;

use LDL\File\Collection\ReadableFileCollection;

interface EnvFileFinderInterface
{
    /**
     * @param bool $cache
     * @return ReadableFileCollection
     * @throws Exception\NoFilesFoundException
     */
    public function find(bool $cache = true) : ReadableFileCollection;

    /**
     * @return Options\EnvFileFinderOptionsInterface
     */
    public function getOptions(): Options\EnvFileFinderOptionsInterface;
}