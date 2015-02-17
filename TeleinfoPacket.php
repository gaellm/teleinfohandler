<?php

namespace TeleinfoHandler;

/**
 * Teleinfo packet
 *
 * This class represents a teleinfo packet and provides related methods.
 * 
 * @author     Gaël Le Moëllic <gael.lm@gmail.com>
 * @version    Release: 1.0
 * @since      Class available since Release 1.0
 */
class TeleinfoPacket {
    
    /**
     * Teleinfo packet array
     *
     * @var array teleinfoPacketArray
     * @access private
     */
    private $_teleinfoPacketArray = array();
    
    /**
     * Teleinfo packet date
     *
     * @var int teleinfoPacketDate UNIX timestamp
     * @access private
     */
    private $_teleinfoPacketDate;

    /**
     * Teleinfo packet string
     *
     * @var string teleinfoPacketString
     * @access private
     */
    private $_teleinfoPacketString;
    
    /**
     * Teleinfo packet json
     *
     * @var string teleinfoPacketJson
     * @access private
     */
    private $_teleinfoPacketJson = '';

    /**
     * Constructor.
     *
     * @param string $teleinfoPacketString teleinfo packet
     * @param int $teleinfoPacketDate UNIX timestamp
     *
     * @access public
     */
    public function __construct($teleinfoPacketString, $teleinfoPacketDate) {

        $this->setTeleinfoPacketString($teleinfoPacketString);
        $this->setTeleinfoPacketDate($teleinfoPacketDate);
    }
    
    /**
     * Get the teleinfo packet array.
     *
     * @return array teleinfo packet array
     * @access public
     */
    public function getTeleinfoPacketArray() {

        if (empty($this->_teleinfoPacketArray)) {

            $this->createTeleinfoPacketArray();
        }

        return $this->_teleinfoPacketArray;
    }
    
    /**
     * Get the teleinfo packet date.
     *
     * @return string teleinfo packet date GM formatted
     * @access public
     */
    public function getTeleinfoPacketDate() {

       return gmdate("Y-m-d\TH:i:s\Z", $this->_teleinfoPacketDate);
    }

    /**
     * Get the teleinfo packet string.
     *
     * @return string teleinfo packet string
     * @access public
     */
    public function getTeleinfoPacketString() {

        return $this->_teleinfoPacketString;
    }
    
    /**
     * Get the teleinfo packet json.
     *
     * @return string teleinfo packet json string
     * @access public
     */
    public function getTeleinfoPacketJson() {

        if (empty($this->_teleinfoPacketJson)) {

            $this->createTeleinfoPacketJson();
        }

        return $this->_teleinfoPacketJson;
    }

    /**
     * Create the teleinfo packet array.
     *
     * @access private
     */
    private function createTeleinfoPacketArray() {

        $teleinfoPacketString = $this->getTeleinfoPacketString();
        $teleinfoPacketArray['DATE'] = $this->getTeleinfoPacketDate();

        //delete start and end chars
        $teleinfoPacketString = chop(substr($teleinfoPacketString, 1, -1));

        //export packet datas
        $datas = explode(chr(10), $teleinfoPacketString);

        foreach ($datas as $key => $data) {

            //explode data key, data value and data checksome
            $data = explode(' ', $data, 3);

            if (!empty($data[0]) && !empty($data[1])) {
                $dataKey = $data[0];
                $dataValue = $data[1];

                // built array
                $teleinfoPacketArray[$dataKey] = $dataValue;
            }
        }

        $this->setTeleinfoPacketArray($teleinfoPacketArray);
    }

    /**
     * Create the teleinfo packet json formated.
     *
     * @access private
     */
    private function createTeleinfoPacketJson() {

        $teleinfoPacketArray = $this->getTeleinfoPacketArray();

        $this->setTeleinfoPacketJson(json_encode($teleinfoPacketArray));
    }
    
    /**
     * Set the teleinfo packet array.
     *
     * @param array $teleinfoPacketArray teleinfo packet array
     * 
     * @throws InvalidArgumentException when method parameter isn't an array.
     *
     * @access private
     */
    private function setTeleinfoPacketArray($teleinfoPacketArray) {
        
        if(is_array($teleinfoPacketArray)){
            $this->_teleinfoPacketArray = $teleinfoPacketArray;
        } else {
            throw new \InvalidArgumentException('teleinfoPacketArray parameter must be an array');
        }
    }

    /**
     * Set the teleinfo packet date.
     *
     * @param int $teleinfoPacketDate UNIX timestamp
     * 
     * @throws InvalidArgumentException when method parameter isn't an integer.
     *
     * @access private
     */
    private function setTeleinfoPacketDate($teleinfoPacketDate) {

        if (is_int($teleinfoPacketDate)) {
            $this->_teleinfoPacketDate = $teleinfoPacketDate;
        } else {
            throw new \InvalidArgumentException('teleinfoPacketDate parameter must be an integer, and represent UNIX timestamp');
        }
    }
    
    /**
     * Set the teleinfo packet string.
     *
     * @param string $teleinfoPacketString teleinfo packet string
     * 
     * @throws InvalidArgumentException when method parameter isn't a string.
     *
     * @access private
     */
    private function setTeleinfoPacketString($teleinfoPacketString) {

        if (is_string($teleinfoPacketString)) {
            $this->_teleinfoPacketString = $teleinfoPacketString;
        } else {
            throw new \InvalidArgumentException('teleinfoPacketString parameter must be a string');
        }
    }

    /**
     * Set the teleinfo packet json.
     *
     * @param string $teleinfoPacketJson teleinfo packet json
     * 
     * @throws InvalidArgumentException when method parameter isn't a string.
     *
     * @access private
     */
    private function setTeleinfoPacketJson($teleinfoPacketJson) {
        
        if(is_string($teleinfoPacketJson)){
            $this->_teleinfoPacketJson = $teleinfoPacketJson;
        } else {
            throw new \InvalidArgumentException('teleinfoPacketJson parameter must be a string');
        }  
    }

}
