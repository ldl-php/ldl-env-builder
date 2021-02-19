<?php declare(strict_types=1);

namespace LDL\Env\Builder;

use LDL\Env\Builder\Config\EnvBuilderConfig;
use LDL\Env\Builder\Config\EnvBuilderConfigInterface;
use LDL\Env\Util\Line\Collection\EnvLineCollectionInterface;

abstract class EnvBuilder implements EnvBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public static function build(EnvBuilderConfigInterface $config=null): EnvLineCollectionInterface
    {
        $config   = $config ?? new EnvBuilderConfig();

        $parser   = $config->getParser();
        $finder   = $config->getFinder();

        return $parser->parse($finder->find());
    }

}