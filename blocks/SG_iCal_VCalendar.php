<?php // BUILD: Remove line

/**
 * The wrapper for the main vcalendar data. Used instead of ArrayObject
 * so you can easily query for title and description.
 * Exposes a iterator that will loop though all the data
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_VCalendar {
	// PHP4 doesn't have iterators… this will need something clever!
	var $data;
	
	/**
	 * Creates a new SG_iCal_VCalendar.
	 */
	function SG_iCal_VCalendar($data) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		$this->data = $data;
	}
	
	/**
	 * Returns the title of the calendar. If no title is known, NULL 
	 * will be returned
	 * @return string
	 */
	function getTitle() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		if( isset($this->data['x-wr-calname']) ) {
			return $this->data['x-wr-calname'];
		} else {
			return null;
		}
	}
	
	/**
	 * Returns the description of the calendar. If no description is
	 * known, NULL will be returned.
	 * @return string
	 */
	function getDescription() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		if( isset($this->data['x-wr-caldesc']) ) {
			return $this->data['x-wr-caldesc'];
		} else {
			return null;
		}
	}
	
	/**
	 * @see IteratorAggregate.getIterator()
	 */
	function getIterator() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return new ArrayIterator($this->data);
	}
}

?>