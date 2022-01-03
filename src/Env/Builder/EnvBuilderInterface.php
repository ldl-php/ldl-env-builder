<?php

declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\Util\File\Exception\ReadEnvFileException;
use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;
use LDL\File\Collection\ReadableFileCollection;

interface EnvBuilderInterface
{
    /**
     * @throws ReadEnvFileException
     */
    public function build(ReadableFileCollection $files): EnvLineCollectionInterface;
}
