<?php declare(strict_types=1);

namespace LDL\Env\Builder\Config;

use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\EnvFileFinderInterface;
use LDL\Env\Util\File\Parser\EnvFileParser;
use LDL\Env\Util\File\Parser\EnvFileParserInterface;
use LDL\Env\Util\File\Writer\EnvFileWriter;
use LDL\Env\Util\File\Writer\EnvFileWriterInterface;
use LDL\Env\Util\Line\Collection\Compiler\EnvCompiler;
use LDL\Env\Util\Line\Collection\Compiler\EnvCompilerInterface;

class EnvBuilderConfig implements EnvBuilderConfigInterface
{
    /**
     * @var EnvFileFinderInterface
     */
    private $finder;

    /**
     * @var EnvCompilerInterface
     */
    private $compiler;

    /**
     * @var EnvFileParserInterface
     */
    private $parser;

    /**
     * @var EnvFileWriterInterface
     */
    private $writer;

    public function __construct(
        EnvFileParserInterface $parser = null,
        EnvFileFinderInterface $finder = null,
        EnvCompilerInterface $compiler = null,
        EnvFileWriterInterface $writer = null
    )
    {
        $this->parser = $parser ?? new EnvFileParser();
        $this->finder = $finder ?? new EnvFileFinder();
        $this->compiler = $compiler ?? new EnvCompiler();
        $this->writer = $writer ?? new EnvFileWriter();
    }

    /**
     * {@inheritdoc}
     */
    public function getParser() : EnvFileParserInterface
    {
        return $this->parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFinder(): EnvFileFinderInterface
    {
        return $this->finder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompiler(): EnvCompilerInterface
    {
        return $this->compiler;
    }

    /**
     * {@inheritdoc}
     */
    public function getWriter() : EnvFileWriterInterface
    {
        return $this->writer;
    }

    public function toArray(): array
    {
        return [
            'finder' => $this->finder->getOptions()->toArray(),
            'parser' => $this->parser->getOptions()->toArray(),
            'writer' => $this->writer->getOptions()->toArray(),
            'compiler' => $this->compiler->getOptions()->toArray()
        ];
    }
}