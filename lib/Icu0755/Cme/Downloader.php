<?php
namespace Icu0755\Cme;

use Icu0755\Cme\Downloader\SaveFile;
use Symfony\Component\Console\Helper\ProgressBar;

class Downloader
{
    protected $curl;

    protected $error;

    protected $data;

    /**
     * @var SaveFile
     */
    protected $save;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    protected $url;

    function __construct($url)
    {
        $this->url = $url;
        $this->save = new SaveFile();
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
        if ($this->curl = curl_init($this->url)) {
            curl_setopt($this->curl, CURLOPT_NOPROGRESS, false);
            curl_setopt($this->curl, CURLOPT_PROGRESSFUNCTION, array($this, 'progressCallback'));
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        }
    }

    protected function finish()
    {
        if ($this->curl) {
            curl_close($this->curl);
        }

        $this->save();
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

    protected function save()
    {
        if ($this->data) {
            $this->save->save($this->data);
        }
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
}