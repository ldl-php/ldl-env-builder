<?php declare(strict_types=1);

namespace LDL\Env\Builder\Config\Writer;


use LDL\Env\Builder\Config\EnvBuilderConfigInterface;

interface EnvBuilderConfigWriterInterface
{
    public static function write(
        EnvBuilderConfigInterface $config,
        string $outFile,
        \DateTimeInterface $date=null
    );
}