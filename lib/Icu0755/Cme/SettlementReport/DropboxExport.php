<?php
namespace Icu0755\Cme\SettlementReport;

use Dropbox\Client;
use Dropbox\Path;
use Dropbox\WriteMode;

class DropboxExport implements HandlerInterface
{
    protected $client;

    protected $dropboxPath;

    protected $sourcePath;

    protected $token;

    public function process($options)
    {
        $this->setOptions($options);
        $this->export();
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setOptions($options)
    {
        if (isset($options['access_token'])) {
            $this->setToken($options['access_token']);
        }

        if (isset($options['sourcePath'])) {
            $this->setSourcePath($options['sourcePath']);
        }

        if (isset($options['dropboxPath'])) {
            $this->setDropboxPath($options['dropboxPath']);
        }
    }

    protected function export()
    {
        $client = $this->getClient();

        $pathError = Path::findErrorNonRoot($this->dropboxPath);
        if ($pathError !== null) {
            throw new \ErrorException('Invalid <dropbox-path>: $pathError');
        }

        $size = null;
        if (\stream_is_local($this->sourcePath)) {
            $size = \filesize($this->sourcePath);
        }

        $fp = fopen($this->sourcePath, "rb");
        $metadata = $client->uploadFile($this->dropboxPath, WriteMode::add(), $fp, $size);
        fclose($fp);
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @param mixed $dropboxPath
     */
    public function setDropboxPath($dropboxPath)
    {
        $now = new \DateTime();
        $dropboxPath = str_replace(':date', $now->format('Ymd'), $dropboxPath);

        $this->dropboxPath = $dropboxPath;
    }

    /**
     * @param mixed $sourcePath
     */
    public function setSourcePath($sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        if (null === $this->client) {
            $this->client = new Client($this->token, get_class());
        }

        return $this->client;
    }
}