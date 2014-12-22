<?php
namespace Icu0755\Cme\SettlementReport;

use Symfony\Component\Console\Helper\ProgressBar;

class ReportImport implements HandlerInterface
{
    protected $curl;

    protected $error;

    protected $data;

    protected $defaultOptions;

    protected $file;

    protected $sourcePath;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    protected $url;

    function __construct()
    {
        $this->defaultOptions = array(
            'reportUrl' => 'ftp://ftp.cmegroup.com/pub/settle/stlcur',
        );
    }

    public function download()
    {
        $this->init();
        $this->start();
        $this->finish();

        return $this->data;
    }

    protected function init()
    {
        $this->file = fopen($this->sourcePath, 'w');
        if ($this->curl = curl_init($this->url)) {
            curl_setopt($this->curl, CURLOPT_NOPROGRESS, false);
            curl_setopt($this->curl, CURLOPT_PROGRESSFUNCTION, array($this, 'progressCallback'));
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_FILE, $this->file);
        }
    }

    protected function finish()
    {
        if ($this->curl) {
            curl_close($this->curl);
        }
        fclose($this->file);
        $this->finishProgress();
    }

    protected function finishProgress()
    {
        if ($this->progressBar) {
            $this->progressBar->finish();
        }
    }

    /**
     * @param mixed $progressBar
     */
    public function setProgressBar($progressBar)
    {
        $this->progressBar = $progressBar;
        $this->startProgress();
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param mixed $sourcePath
     */
    public function setSourcePath($sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    protected function progress($percentage)
    {
        if ($this->progressBar) {
            $this->progressBar->setProgress($percentage);
        }
    }

    protected function progressCallback($curl, $total, $downloaded)
    {
        $percentage = 0;
        if ($total) {
            $percentage = round(100 * $downloaded / $total);
        }

        $this->progress($percentage);
    }

    protected function start()
    {
        $this->data = false;

        if ($this->curl) {
            $this->data = curl_exec($this->curl);

            if (false === $this->data) {
                $this->error = curl_error($this->curl);
            }
        }

        return $this->data;
    }

    protected function startProgress()
    {
        if ($this->progressBar) {
            $this->progressBar->start();
        }
    }

    public function process($options)
    {
        $options = array_merge($this->defaultOptions, $options);
        $this->setOptions($options);

        $this->download();
    }

    public function setOptions($options)
    {
        if (isset($options['progressBar'])) {
            $this->setProgressBar($options['progressBar']);
        }

        if (isset($options['reportUrl'])) {
            $this->setUrl($options['reportUrl']);
        }

        if (isset($options['sourcePath'])) {
            $this->setSourcePath($options['sourcePath']);
        }
    }
}