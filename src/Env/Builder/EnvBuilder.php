<?php declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\File\Finder\EnvFileFinderInterface;
use LDL\Env\Util\Compiler\EnvCompilerInterface;
use LDL\Env\Util\File\Parser\EnvFileParserInterface;
use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;

final class EnvBuilder implements EnvBuilderInterface
{
    /**
     * @var EnvFileParserInterface
     */
    private $parser;

    /**
     * @var EnvFileFinderInterface
     */
    private $finder;

    /**
     * @var EnvCompilerInterface
     */
    private $compiler;

    public function __construct(
        EnvFileFinderInterface $finder,
        EnvFileParserInterface $fileParser,
        EnvCompilerInterface $compiler
    )
    {
        $this->finder = $finder;
        $this->parser = $fileParser;
        $this->compiler = $compiler;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): EnvLineCollectionInterface
    {
        return $this->compiler->compile(
            $this->parser->parse(
                $this->finder->find()
            )
        );
    }

}