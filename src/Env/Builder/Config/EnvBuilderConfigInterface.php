<?php declare(strict_types=1);

namespace LDL\Env\Builder\Config;

use LDL\Env\File\Finder\EnvFileFinderInterface;
use LDL\Env\Util\File\Parser\EnvFileParserInterface;
use LDL\Env\Util\File\Writer\EnvFileWriterInterface;
use LDL\Env\Util\Line\Collection\Compiler\EnvCompilerInterface;

interface EnvBuilderConfigInterface
{
    /**
     * @return EnvFileParserInterface
     */
    public function getParser(): EnvFileParserInterface;

    /**
     * @return EnvFileFinderInterface
     */
    public function getFinder(): EnvFileFinderInterface;

    /**
     * @return EnvCompilerInterface
     */
    public function getCompiler(): EnvCompilerInterface;

    /**
     * @return EnvFileWriterInterface
     */
    public function getWriter() : EnvFileWriterInterface;

    /**
     * @return array
     */
    public function toArray() : array;

}