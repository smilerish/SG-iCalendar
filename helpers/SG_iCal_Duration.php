<?php // BUILD: Remove line

/**
 * A class for calculating how many seconds a duration-string is
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */

class SG_iCal_Duration {
	var $dur;
	
	/**
	 * Constructs a new SG_iCal_Duration from a duration-rule.
	 * The basic build-up of DURATIONs are:
	 *  (["+"] / "-") "P" (dur-date / dur-date + "T" + dur-time / dur-time / dur-week)
	 * Is solved via a really fugly reg-exp with way to many ()'s..
	 *
	 * @param $duration string
	 */
	function SG_iCal_Duration( $duration ) {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		if( $duration{0} == 'P' || (($duration{0} == '+' || $duration{0} == '-') && $duration{1} == 'P') ) {
			preg_match('/P((\d+)W)?((\d+)D)?(T)?((\d+)H)?((\d+)M)?((\d+)S)?/', $duration, $matches);
			$results = array('weeks'=>(int)$matches[2],
							'days'=>(int)$matches[4],
							'hours'=>(int)$matches[7],
							'minutes'=>(int)$matches[9],
							'seconds'=>(int)$matches[11]);
			
			$ts = 0;
			$ts += $results['seconds'];
			$ts += 60 * $results['minutes'];
			$ts += 60 * 60 * $results['hours'];
			$ts += 24 * 60 * 60 * $results['days'];
			$ts += 7 * 24 * 60 * 60 * $results['weeks'];				
							
			$dir = ($duration{0} == '-') ? -1 : 1;
			
			$this->dur = $dir * $ts;
		} else {
			// Invalid duration!
			$this->dur = 0;
		}
	}
	
	/**
	 * Returns the duration in seconds
	 * @return int
	 */
	function getDuration() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		return $this->dur;
	}
}
