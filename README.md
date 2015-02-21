Teleinfo Handler API
=====================

TeleinfoHandler is a PHP API to handle EDF teleinfo data flow, and manage real time events. The API permit to simlpy retrieves teleinfo packets using the design pattern observer / listener in different PHP threads.
To see teleinfo documentation : http://norm.edf.fr/pdf/HN44S812emeeditionMars2007.pdf

###How it works
TeleinfoHandler object reads the teleinfo buffer giving in argument, and on each events (only RECEIVED for this version), creates a new thread and notify observers, giving teleinfo packet and event type. Then you will be able to follow up with your own code and the packet received. Packet is transmit with TeleinfoPacket object, provinding the packet string and some methods (see TeleinfoPacket.php class).
To use the API you need to install pthreads PHP's driver (http://php.net/manual/en/book.pthreads.php). Then you can create your observer class, TeleinfoHandlerObserver extending is mandatory for teleinfo handler observering.

observer sample:

```
<?php
/**
 * TeleinfoObserver.php
 */

include_once 'TeleinfoHandler/TeleinfoHandlerObserver.php';

use TeleinfoHandler\TeleinfoHandlerObserver as TeleinfoHandlerObserver;

/**
 * Teleinfo observer
 *
 * This class observers teleinfo handler events. TeleinfoHandlerObserver
 * extending is mandatory for teleinfo handler observering.
 * 
 * @author     Gaël Le Moëllic <gael.lm@gmail.com>
 * @version    Release: 1.0
 * @since      Class available since Release 1.0
 */
class TeleinfoObserver extends TeleinfoHandlerObserver {
    
   
    /**
     * This method will be called when the observer is notify by a RECEIVED event.
     *
     * @access protected
     */
    protected function onReceivedEvent() {
        
        //do somthing
        print($this->_teleinfoPacket->getTeleinfoPacketJson()."\xA");
    }
    
}
```

Then to use your observer. See the following instantiation sample :

```
<?php

include_once 'TeleinfoHandler/TeleinfoHandler.php';
include_once 'TeleinfoObserver.php';

use TeleinfoHandler\TeleinfoHandler as TeleinfoHandler;

//create the observer
$obs = new TeleinfoObserver();

//create the handler, providing teleinfo dataflow buffer path
$handler = new TeleinfoHandler('/dev/ttyAMA0');
$handler->addObserver($obs);

//start reading
$handler->run();
```

###Dependencies
Tested with:
* PHP 5.6.5