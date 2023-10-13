<?php

namespace Tine20\VObject\Property\ICalendar;

use Tine20\VObject\Component\VCalendar;
use Tine20\VObject\Component\VEvent;

class DurationTest extends \PHPUnit_Framework_TestCase {

    function testGetDateInterval() {

        $vcal = new VCalendar();
        $event = $vcal->add('VEVENT', array('DURATION' => array('PT1H')));

        $this->assertEquals(
            new \DateInterval('PT1H'),
            $event->{'DURATION'}->getDateInterval()
        );
    }
} 
