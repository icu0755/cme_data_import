<?php
use Icu0755\Console\Command\DownloadCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

if (!defined('APP_PATH')) {
    define('APP_PATH', __DIR__);
}

$console = new Application();
$console->add(new DownloadCommand());
$console->run();