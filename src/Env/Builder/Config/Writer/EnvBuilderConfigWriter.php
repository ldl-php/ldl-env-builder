<?php declare(strict_types=1);

namespace LDL\Env\Builder\Config\Writer;

use LDL\Env\Builder\Config\EnvBuilderConfigInterface;

class EnvBuilderConfigWriter
{
    public static function write(
        EnvBuilderConfigInterface $config,
        string $outFile,
        \DateTimeInterface $date=null
    )
    {
        $date = $date ?? new \DateTime('now', new \DateTimeZone('UTC'));

        $arrayConfig = ['generation' => [
                'file' => $outFile,
                'date' => $date->format(\DATE_ATOM),
                'user' => function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'UNKNOWN'
            ]
        ] + $config->toArray();

        if(
            !is_writable(dirname($outFile)) ||
            (file_exists($outFile) && false === is_writable($outFile)) || !is_writable(dirname($outFile))
        ){
            $msg = "Can not write builder config to file: \"$outFile\", check your permissions";
            throw new Exception\PermissionsException($msg);
        }

        file_put_contents($outFile, json_encode($arrayConfig,\JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT));
    }
}