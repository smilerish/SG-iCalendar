<?php // BUILD: Remove line

/**
 * A simple Factory for converting a section/data pair into the
 * corresponding block-object. If the section isn't known a simple
 * ArrayObject is used instead.
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_Factory {
	/**
	 * Returns a new block-object for the section/data-pair. The list
	 * of returned objects is:
	 * 
	 * vcalendar => SG_iCal_VCalendar
	 * vtimezone => SG_iCal_VTimeZone
	 * vevent => SG_iCal_VEvent
	 * * => ArrayObject
	 *
	 * @param $ical SG_iCalReader The reader this section/data-pair belongs to
	 * @param $section string
	 * @param SG_iCal_Line[]
	 */
	function factory( $ical, $section, $data ) {
		if( ! is_a($ical,'SG_iCal') )
			die('$ical is not an instance of SG_iCal in '.__FILE__.':'.__LINE__);
		
		switch( $section ) {
			case "vcalendar":
				require_once dirname(__FILE__).'/../blocks/SG_iCal_VCalendar.php'; // BUILD: Remove line
				return new SG_iCal_VCalendar(SG_iCal_Line::Remove_Line($data), $ical );
			case "vtimezone":
				require_once dirname(__FILE__).'/../blocks/SG_iCal_VTimeZone.php'; // BUILD: Remove line
				return new SG_iCal_VTimeZone(SG_iCal_Line::Remove_Line($data), $ical );
			case "vevent":
				require_once dirname(__FILE__).'/../blocks/SG_iCal_VEvent.php'; // BUILD: Remove line
				return new SG_iCal_VEvent($data, $ical );
			
			default:
				return new ArrayObject(SG_iCal_Line::Remove_Line((array) $data) );
		}
	}
}
