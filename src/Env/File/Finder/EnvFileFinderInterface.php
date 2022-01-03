<?php

declare(strict_types=1);

namespace LDL\Env\File\Finder;

use LDL\File\Collection\ReadableFileCollection;

interface EnvFileFinderInterface
{
    public function find(bool $cache = true): ReadableFileCollection;

    public function getOptions(): Options\EnvFileFinderOptionsInterface;
}
