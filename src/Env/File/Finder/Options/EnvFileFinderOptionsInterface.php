<?php declare(strict_types=1);

namespace LDL\Env\File\Finder\Options;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Contracts\ToArrayInterface;

interface EnvFileFinderOptionsInterface extends ArrayFactoryInterface, ToArrayInterface, \JsonSerializable
{
    /**
     * @return array
     */
    public function getFiles(): array;

    /**
     * @return array
     */
    public function getDirectories(): array;

    /**
     * @return array
     */
    public function getExcludedDirectories(): array;

    /**
     * @return array
     */
    public function getExcludedFiles(): array;

    /**
     * @param EnvFileFinderOptionsInterface $options
     * @return EnvFileFinderOptionsInterface
     */
    public function merge(EnvFileFinderOptionsInterface $options);
}