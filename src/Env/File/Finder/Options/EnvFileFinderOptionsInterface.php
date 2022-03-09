<?php

declare(strict_types=1);

namespace LDL\Env\File\Finder\Options;

use LDL\File\Contracts\FileInterface;
use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFactoryInterface;
use LDL\Framework\Base\Contracts\JsonFileFactoryInterface;
use LDL\Framework\Base\Contracts\Type\ToArrayInterface;

interface EnvFileFinderOptionsInterface extends ArrayFactoryInterface, ToArrayInterface, \JsonSerializable, JsonFactoryInterface, JsonFileFactoryInterface
{
    public function getFiles(): array;

    public function getDirectories(): array;

    public function getExcludedDirectories(): array;

    public function getExcludedFiles(): array;

    /**
     * @return EnvFileFinderOptionsInterface
     */
    public function merge(EnvFileFinderOptionsInterface $options);

    public function write(string $file): FileInterface;
}
