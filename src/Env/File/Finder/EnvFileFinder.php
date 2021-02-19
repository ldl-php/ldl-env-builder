<?php declare(strict_types=1);

namespace LDL\Env\File\Finder;

use LDL\File\Collection\ReadableFileCollection;
use LDL\File\Constants\FileTypeConstants;
use LDL\File\Finder\Adapter\Local\Facade\LocalFileFinderFacade;
use LDL\File\Finder\Adapter\Local\LocalFileFinderAdapter;
use LDL\File\Finder\FoundFile;
use LDL\File\Validator\FileNameValidator;
use LDL\File\Validator\FileTypeValidator;
use LDL\Framework\Helper\IterableHelper;
use LDL\Validators\Chain\AndValidatorChain;
use LDL\Validators\Chain\Item\Collection\ValidatorChainItemCollection;
use LDL\Validators\Chain\OrValidatorChain;

class EnvFileFinder implements EnvFileFinderInterface
{
    /**
     * @var Options\EnvFileFinderOptionsInterface
     */
    private $options;

    /**
     * @var ReadableFileCollection
     */
    private $files;

    public function __construct(Options\EnvFileFinderOptionsInterface $options = null)
    {
        $this->options = $options ?? Options\EnvFileFinderOptions::fromArray([]);
        $this->files = new ReadableFileCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function find(bool $cache = false) : ReadableFileCollection
    {
        if(true === $cache){
            return $this->files;
        }

        $this->files = clone($this->files);

        $options = $this->options;

        $validators = new ValidatorChainItemCollection([
            new FileTypeValidator([FileTypeConstants::FILE_TYPE_REGULAR])
        ]);

        foreach($this->options->getFiles() as $file){
            $validators->append(new FileNameValidator($file));
        }

        $validators = new AndValidatorChain($validators);

        $excludedChain = new AndValidatorChain();

        if(count($options->getExcludedDirectories()) > 0){
            $excludedDirsNameChain = new OrValidatorChain();

            foreach($options->getExcludedDirectories() as $dir){
                $excludedDirsNameChain->getChainItems()->append(new FileNameValidator($dir, true));
            }

            $excludedChain->getChainItems()->append($excludedDirsNameChain);
        }

        if(count($options->getExcludedFiles()) > 0){
            $excludedFilesNameChain = new OrValidatorChain();

            foreach($options->getExcludedFiles() as $file){
                $excludedFilesNameChain->getChainItems()->append(new FileNameValidator($file, true));
            }

            $excludedChain->getChainItems()->append($excludedFilesNameChain);
        }

        if(count($excludedChain->getChainItems()) > 0) {
            $validators->getChainItems()->append($excludedChain);
        }

        $filesNameChain = new OrValidatorChain();

        foreach($options->getFiles() as $file){
            $filesNameChain->getChainItems()->append(new FileNameValidator($file));
        }

        $validators->getChainItems()->append($filesNameChain);

        $finder = new LocalFileFinderAdapter($validators);
        $foundFiles = iterator_to_array($finder->find($this->options->getDirectories()), false);

        if(!count($foundFiles)){
            $msg = sprintf(
                'No files were found matching: "%s" in directories: "%s"',
                implode(', ', $options->getFiles()),
                implode(', ', $options->getDirectories())
            );

            throw new Exception\NoFilesFoundException($msg);
        }

        /**
         * @var FoundFile $foundFile
         */
        foreach($foundFiles as $foundFile){
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