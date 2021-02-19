<?php declare(strict_types=1);

use LDL\Env\File\Finder\EnvFileFinder;
use LDL\Env\File\Finder\Options\EnvFileFinderOptions;

require __DIR__.'/../vendor/autoload.php';

try{
    echo "[ Building compiled env file ]\n";

    $envFileFinder = new EnvFileFinder(EnvFileFinderOptions::fromArray([
        'directories' => [__DIR__.'/Application/Admin'],
        'excludedDirectories' => [__DIR__.'/Application/User']
    ]));

    foreach($envFileFinder->find() as $file){
        echo $file."\n";
    }

}catch(\Exception $e) {

    echo $e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
    echo "[ Build failed! ]\n";
    return;

}

