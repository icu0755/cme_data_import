<?php
namespace Icu0755\Cme\SettlementReport;

use Swift_Mailer;
use Swift_SmtpTransport;

class MailExport implements HandlerInterface
{
    protected $transport;

    protected $mailer;

    protected $encryption = 'tls';

    protected $authMode = 'login';

    protected $host = 'smtp.gmail.com';

    protected $port = 587;

    protected $username;

    protected $password;

    protected $filename;

    public function process($options)
    {
        $this->setOptions($options);
        $this->export();
    }

    public function check()
    {
        if (!file_exists($this->filename)) {
            throw new \ErrorException('The file ' . $this->filename . ' does not exist!');
        }
    }

    public function export()
    {
        $this->check();
        $mailer = $this->getMailer();
        $mailer->send($this->createMessage());
    }

    public function createMessage()
    {
        $now = new \DateTime();
        $message = \Swift_Message::newInstance();
        $message->setSubject('FX Settlement ' . $now->format('Y-m-d'))
            ->setFrom('icu0755@gmail.com', 'Vladimir Ivanov')
            ->setTo('icu0755@gmail.com', 'Vladimir Ivanov')
            ->attach(\Swift_Attachment::fromPath($this->getFilename()));

        return $message;
    }

    public function setOptions($options)
    {
        if (isset($options['encryption'])) {
            $this->setEncryption($options['encryption']);
        }

        if (isset($options['authMode'])) {
            $this->setAuthMode($options['authMode']);
        }

        if (isset($options['host'])) {
            $this->setHost($options['host']);
        }

        if (isset($options['port'])) {
            $this->setPort($options['port']);
        }

        if (isset($options['username'])) {
            $this->setUsername($options['username']);
        }

        if (isset($options['password'])) {
            $this->setPassword($options['password']);
        }

        if (isset($options['sourcePath'])) {
            $this->setFilename($options['sourcePath']);
        }
    }

    public function getTransport()
    {
        if (null == $this->transport) {
            $this->transport = Swift_SmtpTransport::newInstance($this->getHost(), $this->getPort(), $this->getEncryption())
                                                  ->setUsername($this->getUsername())
                                                  ->setPassword($this->getPassword());
        }

        return $this->transport;
    }

    public function getMailer()
    {
        if (null == $this->mailer) {

            $transport    = $this->getTransport();
            $this->mailer = Swift_Mailer::newInstance($transport);

        }

        return $this->mailer;
    }

    /**
     * @return string
     */
    public function getEncryption()
    {
        return $this->encryption;
    }

    /**
     * @param string $encryption
     */
    public function setEncryption($encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * @return string
     */
    public function getAuthMode()
    {
        return $this->authMode;
    }

    /**
     * @param string $authMode
     */
    public function setAuthMode($authMode)
    {
        $this->authMode = $authMode;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }
}