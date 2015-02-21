<?php

namespace TeleinfoHandler;

include_once 'TeleinfoHandlerEventsInterface.php';
include_once 'TeleinfoHandlerObserver.php';
include_once 'TeleinfoHandlerObserverThreadLauncher.php';
include_once 'TeleinfoPacket.php';

/**
 * Handle teleinfo dataflow
 *
 * This class read the teleinfo buffer, and notify observers using TeleinfoHandlerObserverThreadLauncher
 * to not disrupt reading process and create new threads. TeleinfoHandler implements
 * TeleinfoHandlerEventsInterface which lists teleinfo events. 
 * 
 * @link http://norm.edf.fr/pdf/HN44S812emeeditionMars2007.pdf teleinfo technical specifications
 * 
 * @api
 * 
 * @author     Gaël Le Moëllic <gael.lm@gmail.com>
 * @version    Release: 1.0
 * @since      Class available since Release 1.0
 */
class TeleinfoHandler implements TeleinfoHandlerEventsInterface {

    /**
     * Observers list
     *
     * @var observers
     * @access private
     */
    private $_observers = array();

    /**
     * Teleinfo buffer path
     *
     * @var teleinfoBuffer
     * @access private
     */
    private $_teleinfoBuffer;

    /**
     * Constructor.
     *
     * @param string $teleinfoBuffer path to the file containing teleinfo buffer, this 
     *                               path is set by the following command line example :
     *                               "stty -F /dev/ttyAMA0 1200 sane evenp parenb cs7 -crtscts"
     *                               that create the buffer "/dev/ttyAMA0"
     *
     * @access public
     */
    public function __construct($teleinfoBuffer) {

        $this->setTeleinfoBuffer($teleinfoBuffer);
    }

    /**
     * Add an observer.
     * 
     * Add an observer that will be called on each teleinfo event. Observer must 
     * extends TeleinfoHandlerObserver.
     *
     * @param TeleinfoHandlerObserver $observer observer to add
     *
     * @access public
     */
    public function addObserver(TeleinfoHandlerObserver $observer) {

        $this->_observers [] = $observer;
    }

    /**
     * Run handler reading process
     * 
     * This method open the data flow buffer, wait a packet end, and start to read
     * and notify observers.
     * 
     * @access public
     */
    public function run() {

        //open data flow
        $teleinfoBuffer = $this->_teleinfoBuffer;
        $handle = fopen($teleinfoBuffer, "r");

        //wait packet end to start process on the next one
        while (fread($handle, 1) != chr(2));

        //Run
        $char = '';
        $teleinfoPacketString = '';
        while (1) {
            $time = time();
            
            //read all char until the packet end
            while ($char != chr(2)) {
                $char = fread($handle, 1);
                if ($char != chr(2)) {
                    $teleinfoPacketString .= $char;
                }
            }

            $teleinfoPacket = new TeleinfoPacket($teleinfoPacketString, $time);

            //notify observers
            $this->notifyObservers(self::EVENT_RECEIVED, $teleinfoPacket);
            $teleinfoPacketString = '';
            $char = '';
        }
    }

    /**
     * Notify observers on teleinfo handler events.
     *
     * @param string $event teleinfo event source
     * @param TeleinfoPacket $teleinfoPacket teleinfo packet
     *
     * @throws InvalidArgumentException if the event parameter is unknown
     * @return boolean true if operation succeed
     * 
     * @access private
     */
    private function notifyObservers($event, TeleinfoPacket $teleinfoPacket) {

        if ($event === self::EVENT_RECEIVED) {
            foreach ($this->_observers as $observer) {

                $observer->setTeleinfoPacket($teleinfoPacket);
                $observer->setTeleinfoEvent($event);

                //clone observer object to prevent asynchrone issues
                $observerThread = new TeleinfoHandlerObserverThreadLauncher(clone $observer);
                return $observerThread->start();
            }
            return true;
        }

        throw new \InvalidArgumentException('event parameter: ' . $event . ' is unknown');
    }

    /**
     * Set teleinfo buffer path.
     *
     * @param string $teleinfoBuffer path to the file containing teleinfo buffer, this 
     *                               path is set by the following command line example :
     *                               "stty -F /dev/ttyAMA0 1200 sane evenp parenb cs7 -crtscts"
     *                               that create the buffer "/dev/ttyAMA0"
     * 
     * @throws InvalidArgumentException if the provided argument is not of type
     *         'string'.
     * @throws Exception if the provided argument file's path doesn't exist.
     *
     * @access private
     */
    private function setTeleinfoBuffer($teleinfoBuffer) {

        if (is_string($teleinfoBuffer)) {
            if (file_exists($teleinfoBuffer)) {
                $this->_teleinfoBuffer = $teleinfoBuffer;
            } else {
                throw new Exception('teleinfoBuffer file "' . $teleinfoBuffer . '" doesn\'t exists');
            }
        } else {
            throw new \InvalidArgumentException('teleinfoBuffer parameter must be a string');
        }
    }

}
