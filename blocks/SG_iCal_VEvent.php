<?php // BUILD: Remove line
define('SG_iCAL_VEVENT__DEFAULT_CONFIRMED',true);
/**
 * The wrapper for vevents. Will reveal a unified and simple api for 
 * the events, which include always finding a start and end (except
 * when no end or duration is given) and checking if the event is 
 * blocking or similar.
 *
 * Will apply the specified timezone to timestamps if a tzid is 
 * specified
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_VEvent {
	var $uid;
	var $start;
	var $end;
	var $recurrence;
	var $summary;
	var $description;
	var $location;
	var $data;
	
	/**
	 * Constructs a new SG_iCal_VEvent. Needs the SG_iCalReader 
	 * supplied so it can query for timezones.
	 * @param SG_iCal_Line[] $data
	 * @param SG_iCalReader $ical
	 */
	function SG_iCal_VEvent($data, $ical ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);
		if( ! is_a($ical,'SG_iCal') )
			die('$ical is not an instance of SG_iCal in '.__FILE__.':'.__LINE__);
		
		$this->uid = $data['uid']->getData();
		unset($data['uid']);

		if ( isset($data['rrule']) ) {
			require_once dirname(__FILE__).'/../helpers/SG_iCal_Recurrence.php'; // BUILD: Remove line
			$this->recurrence = new SG_iCal_Recurrence($data['rrule']);
			unset($data['rrule']);
		}
		
		if( isset($data['dtstart']) ) {
			$this->start = $this->getTimestamp( $data['dtstart'], $ical );
			unset($data['dtstart']);
		}
		
		if( isset($data['dtend']) ) {
			$this->end = $this->getTimestamp($data['dtend'], $ical);
			unset($data['dtend']);
		} elseif( isset($data['duration']) ) {
			require_once dirname(__FILE__).'/../helpers/SG_iCal_Duration.php'; // BUILD: Remove line
			$dur = new SG_iCal_Duration( $data['duration']->getData() );
			$this->end = $this->start + $dur->getDuration();
			unset($data['duration']);
		} elseif ( isset($this->recurrence) ) {
			//if there is a recurrence rule
			$until = $this->recurrence->getUntil();
			$count = $this->recurrence->getCount();
			//check if there is either 'until' or 'count' set
			if ( $this->recurrence->getUntil() or $this->recurrence->getCount() ) {
				//if until is set, set that as the end date (using getTimeStamp)
				if ( $until ) {
					$this->end = strtotime( $until );
				}
				//if count is set, then figure out the last occurrence and set that as the end date
			}
			
		}

		$imports = array('summary','description','location');
		foreach( $imports AS $import ) {
			if( isset($data[$import]) ) {
				$this->$import = $data[$import]->getData();
				unset($data[$import]);
			}
		}
		
		$this->data = SG_iCal_Line::Remove_Line($data);
	}
	
	/**
	 * Returns the UID of the event
	 * @return string
	 */
	function getUID() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->uid;
	}
	
	/**
	 * Returns the summary (or null if none is given) of the event
	 * @return string
	 */
	function getSummary() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->summary;
	}
	
	/**
	 * Returns the description (or null if none is given) of the event
	 * @return string
	 */
	function getDescription() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->description;
	}
	
	/**
	 * Returns the location (or null if none is given) of the event
	 * @return string
	 */
	function getLocation() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->location;
	}
	
	/**
	 * Returns true if the event is blocking (ie not transparent)
	 * @return bool
	 */
	function isBlocking() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return !(isset($this->data['transp']) && $this->data['transp'] == 'TRANSPARENT');
	}
	
	/**
	 * Returns true if the event is confirmed
	 * @return bool
	 */
	function isConfirmed() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		if( !isset($this->data['status']) ) {
			return SG_iCAL_VEVENT__DEFAULT_CONFIRMED;
		} else {
			return $this->data['status'] == 'CONFIRMED';
		}
	}
	
	/**
	 * Returns the timestamp for the beginning of the event
	 * @return int
	 */
	function getStart() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->start;
	}
	
	/**
	 * Returns the timestamp for the end of the event
	 * @return int
	 */
	function getEnd() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->end;
	}
	
	/**
	 * Returns the duration of this event in seconds
	 * @return int
	 */
	function getDuration() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->end - $this->start;
	}
	
	/**
	 * Returns the given property of the event.
	 * @param string $prop
	 * @return string
	 */
	function getProperty( $prop ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		if( isset($this->$prop) ) {
			return $this->$prop;
		} elseif( isset($this->data[$prop]) ) {
			return $this->data[$prop];
		} else {
			return null;
		}
	}
	
	/**
	 * Calculates the timestamp from a DT line.
	 * @param $line SG_iCal_Line
	 * @return int
	 */
	function getTimestamp( $line, $ical ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);
		if( ! is_a($line,'SG_iCal_Line') )
			die('$line is not an instance of SG_iCal_Line in '.__FILE__.':'.__LINE__);
		if( ! is_a($ical,'SG_iCal') )
			die('$ical is not an instance of SG_iCal in '.__FILE__.':'.__LINE__);
		
		$ts = strtotime($line->getData());
		if( isset($line['tzid']) ) {
			$tz = $ical->getTimeZoneInfo($line['tzid']);
			$offset = $tz->getOffset($ts);
			$ts = strtotime(date('D, d M Y H:i:s', $ts) . ' ' . $offset);
		}
		return $ts;
	}
}
