<?php

declare(strict_types=1);

namespace LDL\Env\File\Finder\Options;

use LDL\Env\File\Finder\Options\Exception\EnvFileFinderOptionsException;
use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\File\Collection\DirectoryCollection;
use LDL\File\Contracts\FileInterface;
use LDL\File\File;
use LDL\Framework\Base\Exception\JsonFactoryException;
use LDL\Framework\Base\Exception\JsonFileFactoryException;
use LDL\Type\Collection\Interfaces\Type\StringCollectionInterface;
use LDL\Type\Collection\Types\String\StringCollection;

class EnvFileFinderOptions implements EnvFileFinderOptionsInterface
{
    /**
     * @var DirectoryCollectionInterface
     */
    private $directories;

    /**
     * @var StringCollectionInterface
     */
    private $files = ['.env'];

    /**
     * @var StringCollectionInterface
     */
    private $excludedDirectories;

    /**
     * @var StringCollectionInterface
     */
    private $excludedFiles;

    public function getFiles(): StringCollectionInterface
    {
        return $this->files;
    }

    public function getDirectories(): DirectoryCollectionInterface
    {
        return $this->directories;
    }

    public function getExcludedDirectories(): StringCollectionInterface
    {
        return $this->excludedDirectories;
    }

    public function getExcludedFiles(): StringCollectionInterface
    {
        return $this->excludedFiles;
    }

    public function merge(EnvFileFinderOptionsInterface $options): EnvFileFinderOptionsInterface
    {
        return self::fromArray(
            array_merge($options->toArray(), $this->toArray())
        );
    }

    public function __construct(
        DirectoryCollectionInterface $directories = null,
        StringCollectionInterface $files = null,
        StringCollectionInterface $excludedFiles = null,
        StringCollectionInterface $excludedDirectories = null
    ) {
        $this->directories = $directories ?? new DirectoryCollection();
        $this->files = ($files ?? new StringCollection())->filterEmptyLines();
        $this->excludedFiles = ($excludedFiles ?? new StringCollection())->filterEmptyLines();
        $this->excludedDirectories = ($excludedDirectories ?? new StringCollection())->filterEmptyLines();
    }

    public static function fromArray(array $options = []): EnvFileFinderOptionsInterface
    {
        $merge = array_merge(get_class_vars(__CLASS__), $options);

        return new self(
            new DirectoryCollection($merge['directories']),
            new StringCollection($merge['files']),
            new StringCollection($merge['excludedFiles']),
            new StringCollection($merge['excludedDirectories'])
        );
    }

    public function toArray(bool $useKeys = null): array
    {
        try {
            return [
                'directories' => iterator_to_array($this->directories->getRealPaths()),
                'files' => $this->files->toPrimitiveArray(false),
                'excludedFiles' => $this->excludedFiles->toPrimitiveArray(false),
                'excludedDirectories' => $this->excludedDirectories->toPrimitiveArray(false),
            ];
        } catch (\Throwable $e) {
            $msg = 'Could not convert env file finder options to array';
            throw new EnvFileFinderOptionsException($msg, 0, $e);
        }
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromJsonFile(string $file): EnvFileFinderOptionsInterface
    {
        try {
            return self::fromJsonString((new File($file))->getLinesAsString());
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

    public function write(string $file, bool $force = false): FileInterface
    {
        try {
            return File::create(
                $file,
                json_encode($this, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT),
                0644,
                $force
            );
        } catch (\Throwable $e) {
            $msg = sprintf('Could not write env file finder options to file: %s', $file);
            throw new EnvFileFinderOptionsException($msg, 0, $e);
        }
    }
}
