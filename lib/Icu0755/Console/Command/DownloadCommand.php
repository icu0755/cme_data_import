<?php
namespace Icu0755\Console\Command;

use Icu0755\Cme\Downloader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommand extends Command
{
    protected $handler;

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('cme:download')
            ->setDescription('Download cme settlement data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $this->handler();
        $this->handler->setProgressBar(new ProgressBar($output, 100));
        $handler->download();
    }

    protected function handler($handler = null)
    {
        if (null !== $handler) {
            $this->handler = $handler;
        }

        if (!$this->handler) {
            $this->handler = new Downloader('ftp://ftp.cmegroup.com/pub/settle/stlcur');
        }

        return $this->handler;
    }
}