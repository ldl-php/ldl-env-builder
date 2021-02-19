<?php declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\File\Finder\Exception\NoFilesFoundException;
use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;

interface EnvBuilderInterface
{
    /**
     * @return EnvLineCollectionInterface
     * @throws NoFilesFoundException
     */
    public  function build(): EnvLineCollectionInterface;

}