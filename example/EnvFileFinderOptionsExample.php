<?php

declare(strict_types=1);

use LDL\Env\File\Finder\Options\EnvFileFinderOptions;
use LDL\File\Helper\FileHelper;

require __DIR__.'/../vendor/autoload.php';

try {
    $parameters = [
        'directories' => [__DIR__.'/Application'],
        'excludedDirectories' => ['Application/User'],
    ];

    echo "[ Test env file finder options ]\n";

    echo "Create EnvFileFinderOptions instance with the following parameters:\n\n";

    dump($parameters);

    $options = EnvFileFinderOptions::fromArray($parameters);

    echo "Print parameters from options object: \n\n";

    dump($options->toArray());

    $file = FileHelper::createSysTempFile('env_file_finder_options');

    echo "Write EnvFileFinderOptions to $file\n\n";
    $configFile = $options->write((string) $file, true);

    echo "Recreate EnvFileFinderOptions from previously written file ...\n\n";

    $options = EnvFileFinderOptions::fromJsonFile((string) $file);

    echo "Print options from recreated object:\n\n";

    dump($options->toArray());

    $configFile->delete();
} catch (\Exception $e) {
    echo $e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
    echo "[ Build failed! ]\n";

    return;
}
