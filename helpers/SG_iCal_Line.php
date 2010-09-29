<?php // BUILD: Remove line

/**
 * A class for storing a single (complete) line of the iCal file.
 * Will find the line-type, the arguments and the data of the file and
 * store them.
 * 
 * The line-type can be found by querying getIdent(), data via either
 * getData() or typecasting to a string.
 * Params can be access via the ArrayAccess. A iterator is also avilable
 * to iterator over the params.
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_Line {
	var $ident;
	var $data;
	var $params = array();
	
	var $replacements = array('from'=>array('\\,', '\\n', '\\;', '\\:', '\\"'), 'to'=>array(',', "\n", ';', ':', '"'));
	
	/**
	 * Constructs a new line.
	 */
	function SG_iCal_Line( $line ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		$split = strpos($line, ':');
		$idents = explode(';', substr($line, 0, $split));
		$ident = strtolower(array_shift($idents));
		
		$data = trim(substr($line, $split+1));
		$data = str_replace($this->replacements['from'], $this->replacements['to'], $data);
		
		$params = array();
		foreach( $idents AS $v) {
			list($k, $v) = explode('=', $v);
			$params[ strtolower($k) ] = $v;
		}
		
		$this->ident = $ident;
		$this->params = $params;		
		$this->data = $data;
	}
	
	/**
	 * Is this line the beginning of a new block?
	 * @return bool
	 */
	function isBegin() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->ident == 'begin';
	}
	
	/**
	 * Is this line the end of a block?
	 * @return bool
	 */
	function isEnd() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->ident == 'end';
	}
	
	/**
	 * Returns the line-type (ident) of the line
	 * @return string
	 */
	function getIdent() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->ident;
	}
	
	/**
	 * Returns the content of the line
	 * @return string
	 */
	function getData() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->data;
	}
	
	/**
	 * A static helper to get a array of SG_iCal_Line's, and calls
	 * getData() on each of them to lay the data "bare"..
	 *
	 * @param SG_iCal_Line[]
	 * @return array
	 */
	function Remove_Line($arr) {
		$rtn = array();
		foreach( $arr AS $k => $v ) {
			if(is_array($v)) {
				$rtn[$k] = SG_iCal_Line::Remove_Line($v);
			} elseif( is_a($v,'SG_iCal_Line') ) {
				$rtn[$k] = $v->getData();
			} else {
				$rtn[$k] = $v;
			}
		}
		return $rtn;
	}
	
	/**
	 * @see ArrayAccess.offsetExists
	 */
	function offsetExists( $param ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return isset($this->params[ strtolower($param) ]);
	}
	
	/**
	 * @see ArrayAccess.offsetGet
	 */
	function offsetGet( $param ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		$index = strtolower($param);
		if (isset($this->params[ $index ])) {
			return $this->params[ $index ];
		}
	}
	
	/**
	 * Disabled ArrayAccess requirement
	 * @see ArrayAccess.offsetSet
	 */
	function offsetSet( $param, $val ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return false;
	}
	
	/**
	 * Disabled ArrayAccess requirement
	 * @see ArrayAccess.offsetUnset
	 */
	function offsetUnset( $param ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return false;
	}
	
	/**
	 * toString method.
	 * @see getData()
	 */
	function __toString() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->getData();
	}
	
	/**
	 * @see Countable.count
	 */
	function count() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return count($this->params);
	}
	
	/**
	 * @see IteratorAggregate.getIterator
	 */
	function getIterator() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return new ArrayIterator($this->params);
	}
}
