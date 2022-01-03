<?php

declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\Util\Compiler\EnvCompilerInterface;
use LDL\Env\Util\File\Parser\EnvFileParserInterface;
use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;
use LDL\File\Collection\ReadableFileCollection;

final class EnvBuilder implements EnvBuilderInterface
{
    /**
     * @var EnvFileParserInterface
     */
    private $parser;

    /**
     * @var EnvCompilerInterface
     */
    private $compiler;

    public function __construct(
        EnvFileParserInterface $fileParser,
        EnvCompilerInterface $compiler
    ) {
        $this->parser = $fileParser;
        $this->compiler = $compiler;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ReadableFileCollection $files): EnvLineCollectionInterface
    {
        return $this->compiler->compile(
            $this->parser->parse($files)
        );
    }
}
