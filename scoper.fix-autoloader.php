<?php
/**
 * This helper is needed to "trick" composer autoloader to load the prefixed files
 *
 * More information also found here: https://github.com/humbug/php-scoper/issues/298
 */

$composerFile = __DIR__ . '/composer.json';
$composerDir = realpath(__DIR__ . '/' . trim(shell_exec('composer config vendor-dir')) . '/composer');
$composer = json_decode(file_get_contents($composerFile));

$staticLoaderPath = $composerDir . '/autoload_static.php';
$filesLoaderPath = $composerDir . '/autoload_files.php';

echo "Fixing $staticLoaderPath \n";
$staticLoader = preg_replace(
    '/\'([A-Za-z0-9]*?)\' => __DIR__ \. (.*?),/',
    '\'' . md5($composer->name) . '$1\' => __DIR__ . $2,',
    file_get_contents($staticLoaderPath)
);
file_put_contents($staticLoaderPath, $staticLoader);


echo "Fixing $filesLoaderPath \n";
$filesLoader = preg_replace(
    '/\'(.*?)\' => (.*?),/',
    '\'' . md5($composer->name) . '$1\' => $2,',
    file_get_contents($filesLoaderPath)
);
file_put_contents($filesLoaderPath, $filesLoader);
