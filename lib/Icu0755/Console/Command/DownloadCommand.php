<?php
namespace Icu0755\Console\Command;

use Icu0755\Cme\Downloader;
use Icu0755\Cme\SettlementReport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommand extends Command
{
    protected $handler;

    protected $options;

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    protected function configure()
    {
        $this
            ->setName('cme:settlement:fx')
            ->setDescription('Download FX settlement data');
    }

    protected function loadConfig($config)
    {
        if (!file_exists($config)) {
            throw new \ErrorException("File $config does not exist!");
        }

        $this->options = json_decode(file_get_contents($config), true);
        if (null === $this->options) {
            throw new \ErrorException("Incorrect json format!");
        }

        return $this->options;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfig('config.json');
        $this->setProgressBar($output);
        $this->setSourcePath();
        $this->setDropboxPath();
        $this->runHandler();
    }

    protected function handler($handler = null)
    {
        if (null !== $handler) {
            $this->handler = $handler;
        }

        if (!$this->handler) {
            $settlementReportHandlers = array(
                new SettlementReport\ReportImport(),
                new SettlementReport\DropboxExport(),
            );
            $this->handler = new SettlementReport($settlementReportHandlers);
        }

        return $this->handler;
    }

    protected function runHandler()
    {
        $handler = $this->handler();
        $handler->run($this->getOptions());
    }

    protected function setProgressBar(OutputInterface $output)
    {
        $this->options['progressBar'] = new ProgressBar($output, 100);
    }

    protected function setSourcePath($sourcePath = null)
    {
        if (null === $sourcePath) {
            $sourcePath = tempnam(sys_get_temp_dir(), 'fx');
        }

        $this->options['sourcePath'] = $sourcePath;
    }

    protected function setDropboxPath($dropboxPath = null)
    {
        if (null === $dropboxPath) {
            $dropboxPath = '/:data/stlcur';
        }

        $this->options['dropboxPath'] = $dropboxPath;
    }
}