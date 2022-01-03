<?php

declare(strict_types=1);

namespace LDL\Env\File\Finder;

use LDL\File\Collection\ReadableFileCollection;
use LDL\File\Constants\FileTypeConstants;
use LDL\File\Finder\Adapter\Local\LocalFileFinderAdapter;
use LDL\File\Finder\FoundFile;
use LDL\File\Validator\FileNameValidator;
use LDL\File\Validator\FileTypeValidator;
use LDL\File\Validator\PathValidator;
use LDL\Framework\Base\Collection\CallableCollectionInterface;
use LDL\Validators\Chain\AndValidatorChain;

class EnvFileFinder implements EnvFileFinderInterface
{
    /**
     * @var Options\EnvFileFinderOptionsInterface
     */
    private $options;

    /**
     * @var CallableCollectionInterface
     */
    private $onEnvFileFound;

    /**
     * @var CallableCollectionInterface
     */
    private $onFileRejected;

    /**
     * @var CallableCollectionInterface
     */
    private $onFileFound;

    /**
     * @var ReadableFileCollection
     */
    private $files;

    public function __construct(
        Options\EnvFileFinderOptionsInterface $options = null,
        CallableCollectionInterface $onEnvFileFound = null,
        CallableCollectionInterface $onFileRejected = null,
        CallableCollectionInterface $onFileFound = null
    ) {
        $this->options = $options ?? Options\EnvFileFinderOptions::fromArray([]);

        $this->onEnvFileFound = $onEnvFileFound;
        $this->onFileRejected = $onFileRejected;
        $this->onFileFound = $onFileFound;

        $this->files = new ReadableFileCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function find(bool $cache = false): ReadableFileCollection
    {
        if (true === $cache) {
            return $this->files;
        }

        $this->files = clone $this->files;

        $options = $this->options;

        $validators = new AndValidatorChain([
            new FileTypeValidator([FileTypeConstants::FILE_TYPE_REGULAR]),
        ]);

        if (count($options->getExcludedDirectories()) > 0) {
            foreach ($options->getExcludedDirectories() as $dir) {
                $validators->getChainItems()->append(new PathValidator($dir, true));
            }
        }

        if (count($options->getExcludedFiles()) > 0) {
            foreach ($options->getExcludedFiles() as $file) {
                $validators->getChainItems()->append(new FileNameValidator($file, true));
            }
        }

        foreach ($this->options->getFiles() as $file) {
            $validators->getChainItems()->append(new FileNameValidator($file));
        }

        $finder = new LocalFileFinderAdapter(
            $validators,
            $this->onEnvFileFound,
            $this->onFileRejected,
            $this->onFileFound
        );

        $foundFiles = iterator_to_array($finder->find($this->options->getDirectories()), false);

        if (!count($foundFiles)) {
            return $this->files;
        }

        /**
         * @var FoundFile $foundFile
         */
        foreach ($foundFiles as $foundFile) {
            $this->files->append($foundFile->getPath());
        }

        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Options\EnvFileFinderOptionsInterface
    {
        return $this->options;
    }
}
