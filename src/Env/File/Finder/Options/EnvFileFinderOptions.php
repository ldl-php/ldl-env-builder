<?php

declare(strict_types=1);

namespace LDL\Env\File\Finder\Options;

use LDL\File\Contracts\FileInterface;
use LDL\File\File;
use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\JsonFactoryException;
use LDL\Framework\Base\Exception\JsonFileFactoryException;

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
    ) {
        $this->directories = $directories;
        $this->files = $files;
        $this->excludedDirectories = $excludedDirectories;
        $this->excludedFiles = $excludedFiles;
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

    public function merge(EnvFileFinderOptionsInterface $options): ArrayFactoryInterface
    {
        return self::fromArray(
            array_merge($options->toArray(), $this->toArray())
        );
    }

    public function write(string $path, bool $force = false): FileInterface
    {
        return FileInterface::create(
            $path,
            json_encode($this, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT),
            0644,
            $force
        );
    }

    /**
     * @return EnvFileFinderOptionsInterface
     */
    public static function fromArray(array $options = []): ArrayFactoryInterface
    {
        $k = 'array_key_exists';

        return new self(
            ($k('directories', $options) && is_array($options['directories'])) ? $options['directories'] : [],
            ($k('files', $options) && is_array($options['files'])) ? $options['files'] : ['.env'],
            ($k('excludedDirectories', $options) && is_array($options['excludedDirectories'])) ? $options['excludedDirectories'] : [],
            ($k('excludedFiles', $options) && is_array($options['excludedFiles'])) ? $options['excludedFiles'] : [],
        );
    }

    public function toArray(bool $useKeys = null): array
    {
        return get_object_vars($this);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromJsonFile(string $path): EnvFileFinderOptionsInterface
    {
        try {
            return self::fromJsonString((new File($path))->getLinesAsString());
        } catch (\Throwable $e) {
            throw new JsonFileFactoryException("Could not load config from file $path", 0, $e);
        }
    }

    public static function fromJsonString(string $json): EnvFileFinderOptionsInterface
    {
        try {
            return self::fromArray(json_decode($json, true, 2048, \JSON_THROW_ON_ERROR));
        } catch (\Throwable $e) {
            throw new JsonFactoryException('Could not decode JSON', 0, $e);
        }
    }
}
