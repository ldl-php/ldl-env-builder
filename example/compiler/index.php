<?php

use LDL\Env\Line\EnvLineInterface;
use LDL\Env\Line\Type\Variable\EnvLineVar;
use LDL\Env\Line\Collection\EnvLineCollection;
use LDL\Env\Compiler\Options\EnvCompilerOptions;
use LDL\Env\Compiler\EnvCompiler;
use Symfony\Component\String\UnicodeString;

require __DIR__.'/../../vendor/autoload.php';

$envLineCollection = new EnvLineCollection();

$envLineCollection->appendMany([
    EnvLineVar::createFromString(
        new UnicodeString(sprintf('%s=%s', 'ADMIN', 'admin')),
        1
    ),
    EnvLineVar::createFromString(
        new UnicodeString(sprintf('%s=%s', 'USER', 'user')),
        2
    )
]);

$compilerOptions = EnvCompilerOptions::fromArray([
    'addPrefix' => 'LDL'
]);

$compiler = new EnvCompiler($compilerOptions);
$collection = $compiler->compile($envLineCollection);

echo "Get content of compiled\n";

/**
 * @var EnvLineInterface $envLine
 */
foreach($collection as $envLine){
    echo (string) $envLine;
}