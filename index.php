<?php
use Icu0755\Console\Command\DownloadCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$console = new Application();
$console->add(new DownloadCommand());
$console->run();