<?php
namespace Icu0755\Cme\Downloader;

class SaveFile
{
    protected $dataDir;

    protected $dataFile;

    function __construct()
    {
        $this->dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
        $this->dataFile = 'stlcur';
    }

    public function save($content)
    {
        if (!$this->dataDirExists()) {
            $this->createDataDir();
        }

        $this->saveContent($content);
    }

    protected function saveContent($content)
    {
        chdir($this->dataDir);
        file_put_contents($this->dataFile, $content);
    }

    protected function createDataDir()
    {
        mkdir($this->dataDir);
    }

    protected function dataDirExists()
    {
        return file_exists($this->dataDir);
    }
}