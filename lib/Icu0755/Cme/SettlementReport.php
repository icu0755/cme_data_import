<?php
namespace Icu0755\Cme;

use Icu0755\Cme\SettlementReport\HandlerInterface;

class SettlementReport
{
    protected $handlers;

    function __construct($handlers)
    {
        $this->setHandlers($handlers);
    }


    public function run($options)
    {
        foreach ($this->handlers as $handler) {
            /**
             * @var $handler HandlerInterface
             */
            $handler->process($options);
        }
    }

    /**
     * @param mixed $handlers
     * @throws \ErrorException
     */
    public function setHandlers($handlers)
    {
        if (!is_array($handlers)) {
            $handlers = array($handlers);
        }

        foreach ($handlers as $handler) {
            if (!$handler instanceof HandlerInterface) {
                throw new \ErrorException('Handlers must be instance of HandlerInterface!');
            }
        }

        $this->handlers = $handlers;
    }
}