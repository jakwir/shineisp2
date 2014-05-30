<?php
namespace Base\Listeners;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

class LogListener implements ListenerAggregateInterface
{
    protected $serviceManager;
    
    public function __construct(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager){
        
        $this->serviceManager = $serviceManager;
    }
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $sharedEvents      = $events->getSharedManager();
        
        $this->listeners[] = $sharedEvents->attach('Zend\Mvc\Application', 'dispatch.error', array($this, 'onError'), 100);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onError($e)
    {
       if ($e->getParam('exception')){
            $this->serviceManager->get('Zend\Log\Logger')->crit($e->getParam('exception'));
        }
    }
}