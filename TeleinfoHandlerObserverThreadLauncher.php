<?php

namespace TeleinfoHandler;

include_once 'TeleinfoHandlerObserverInterface.php';

/**
 * Notify teleinfo handler observer
 *
 * This class create a new thread to notify observer on teleinfo events. 
 * TeleinfoHandlerObserverThreadLauncher extends Thread, so need pthreads, an 
 * Object Orientated API that allows user-land multi-threading in PHP.
 * 
 * @link http://php.net/manual/en/book.pthreads.php
 * 
 * @author     Gaël Le Moëllic <gael.lm@gmail.com>
 * @version    Release: 1.0
 * @since      Class available since Release 1.0
 */
class TeleinfoHandlerObserverThreadLauncher extends \Thread {

    private $_observer;

    /**
     * Constructor.
     *
     * @param TeleinfoHandlerObserverInterface $observer observer to thread
     *
     * @access public
     */
    public function __construct(TeleinfoHandlerObserverInterface $observer) {

        $this->_observer = $observer;
    }

    /**
     * This method is called by thread's start method and permit to notify the
     * observer, calling his notify method.
     *
     * @see http://php.net/manual/en/thread.start.php
     * 
     * @access public
     */
    public function run() {
        $this->_observer->notify();
    }

}
