<?php

declare(strict_types=1);

namespace LDL\Env\File\Finder\Options;

use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\File\Contracts\FileInterface;
use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFileFactoryInterface;
use LDL\Framework\Base\Contracts\Type\ToArrayInterface;
use LDL\Type\Collection\Interfaces\Type\StringCollectionInterface;

interface EnvFileFinderOptionsInterface extends ArrayFactoryInterface, ToArrayInterface, \JsonSerializable, JsonFactoryInterface, JsonFileFactoryInterface
{
    public function getFiles(): StringCollectionInterface;

    public function getDirectories(): DirectoryCollectionInterface;

    public function getExcludedDirectories(): StringCollectionInterface;

    public function getExcludedFiles(): StringCollectionInterface;

    public function merge(EnvFileFinderOptionsInterface $options): EnvFileFinderOptionsInterface;

    public function write(string $file, bool $force): FileInterface;
}
