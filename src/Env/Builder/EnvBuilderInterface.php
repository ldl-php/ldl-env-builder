<?php declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\Builder\Config\EnvBuilderConfigInterface;
use LDL\Env\File\Finder\Exception\NoFilesFoundException;
use LDL\Env\Util\File\Writer\Exception\FileAlreadyExistsException;
use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;

interface EnvBuilderInterface
{
    /**
     * @param EnvBuilderConfigInterface $config
     * @return EnvLineCollectionInterface
     * @throws NoFilesFoundException
     * @throws FileAlreadyExistsException
     */
    public static function build(EnvBuilderConfigInterface $config=null): EnvLineCollectionInterface;

}