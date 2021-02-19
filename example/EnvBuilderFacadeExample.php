<?php declare(strict_types=1);

use LDL\Env\Builder\Facade\EnvBuilderFacade;

require __DIR__.'/../vendor/autoload.php';

try{
    echo "[ Building compiled env file ]\n";

    echo EnvBuilderFacade::build([
       __DIR__.'/Application'
    ]);

}catch(\Exception $e) {

    echo $e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
    echo "[ Build failed! ]\n";
    return;

}

