<?php declare(strict_types=1);

namespace LDL\Env\File\Finder\Options;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;

class EnvFileFinderOptions implements EnvFileFinderOptionsInterface
{
    /**
     * @var array
     */
    private $directories;

    /**
     * @var array
     */
    private $files;
    /**
     * @var array
     */
    private $excludedDirectories;

    /**
     * @var array
     */
    private $excludedFiles;

    private function __construct(
        array $directories = [],
        array $files = ['.env'],
        array $excludedDirectories = [],
        array $excludedFiles = []
    )
    {
        $this->directories = $directories;
        $this->files = $files;
        $this->excludedDirectories = $excludedDirectories;
        $this->excludedFiles = $excludedFiles;
    }

    /**
     * @param array $options
     * @return EnvFileFinderOptionsInterface
     */
    public static function fromArray(array $options=[]) : ArrayFactoryInterface
    {
        $k = 'array_key_exists';

        return new self(
            ($k('directories', $options)  && is_array($options['directories'])) ? $options['directories'] : [],
            ($k('files', $options)  && is_array($options['files'])) ? $options['files'] : ['.env'],
            ($k('excludedDirectories', $options)  && is_array($options['excludedDirectories'])) ? $options['excludedDirectories'] : [],
            ($k('excludedFiles', $options)  && is_array($options['excludedFiles'])) ? $options['excludedFiles'] : [],
            );
    }

    public function toArray() : array
    {
        return get_object_vars($this);
    }

    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getDirectories(): array
    {
        return $this->directories;
    }

    public function getExcludedDirectories(): array
    {
        return $this->excludedDirectories;
    }

    public function getExcludedFiles(): array
    {
        return $this->excludedFiles;
    }

    /**
     * @param EnvFileFinderOptionsInterface $options
     * @return EnvFileFinderOptionsInterface
     * @throws \LDL\Framework\Base\Exception\ToArrayException
     */
    public function merge(EnvFileFinderOptionsInterface $options) : ArrayFactoryInterface
    {
        return self::fromArray(
            array_merge($options->toArray(), $this->toArray())
        );
    }
}