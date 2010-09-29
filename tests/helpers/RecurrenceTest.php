<?php
require_once dirname(__FILE__) . '/../common.php';
require_once 'PHPUnit/Framework.php';

class RecurrenceTest extends PHPUnit_Framework_TestCase {

	function testGetFrequencyGetIntervalWithTypicalLineReturnsFrequency() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		$line = new SG_iCal_Line('RRULE:FREQ=DAILY;INTERVAL=2');
		$recurrence = new SG_iCal_Recurrence($line);
		$this->assertEquals('DAILY', $recurrence->getFreq());
		$this->assertEquals('2', $recurrence->getInterval());
	}

	function testGetUntilWithTypicalLineReturnsUntil() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		$line = new SG_iCal_Line('RRULE:FREQ=DAILY;UNTIL=19971224T000000Z');
		$recurrence = new SG_iCal_Recurrence($line);
		$this->assertEquals('19971224T000000Z', $recurrence->getUntil());
	}

	function testGetCountWithTypicalLineReturnsCount() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		$line = new SG_iCal_Line('RRULE:FREQ=WEEKLY;COUNT=10');
		$recurrence = new SG_iCal_Recurrence($line);
		$this->assertEquals('10', $recurrence->getCount());
	}

	function testGetCountWithCountNotSetReturnsFalse() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		$line = new SG_iCal_Line('RRULE:FREQ=DAILY;UNTIL=19971224T000000Z');
		$recurrence = new SG_iCal_Recurrence($line);
		$this->assertSame(false, $recurrence->getCount());
	}

	function testGetByDayReturnsArrayWhenMoreThanOneDayIsIncluded() {
		if( is_null($this) )
			die(__FUNCTION__.' is not static in '.__FILE__.':'.__LINE__);

		$line = new SG_iCal_Line('RRULE:FREQ=WEEKLY;UNTIL=19971007T000000Z;WKST=SU;BYDAY=TU,TH');
		$recurrence = new SG_iCal_Recurrence($line);
		$this->assertEquals(array('TU', 'TH'), $recurrence->getByDay());
		$this->assertEquals('SU', $recurrence->getWkst());
	}

}
