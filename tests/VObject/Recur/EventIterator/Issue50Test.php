<?php

namespace Tine20\VObject;

use
    DateTime,
    DateTimeZone;

class Issue50Test extends \PHPUnit_Framework_TestCase {

    function testExpand() {

        $input = <<<ICS
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Mozilla.org/NONSGML Mozilla Calendar V1.1//EN
BEGIN:VTIMEZONE
TZID:Europe/Brussels
X-LIC-LOCATION:Europe/Brussels
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
CREATED:20130705T142510Z
LAST-MODIFIED:20130715T132556Z
DTSTAMP:20130715T132556Z
UID:1aef0b27-3d92-4581-829a-11999dd36724
SUMMARY:Werken
RRULE:FREQ=DAILY;COUNT=5
DTSTART;TZID=Europe/Brussels:20130715T090000
DTEND;TZID=Europe/Brussels:20130715T170000
LOCATION:Job
DESCRIPTION:Vrij
X-MOZ-GENERATION:9
END:VEVENT
BEGIN:VEVENT
CREATED:20130715T081654Z
LAST-MODIFIED:20130715T110931Z
DTSTAMP:20130715T110931Z
UID:1aef0b27-3d92-4581-829a-11999dd36724
SUMMARY:Werken
RECURRENCE-ID;TZID=Europe/Brussels:20130719T090000
DTSTART;TZID=Europe/Brussels:20130719T070000
DTEND;TZID=Europe/Brussels:20130719T150000
SEQUENCE:1
LOCATION:Job
DESCRIPTION:Vrij
X-MOZ-GENERATION:1
END:VEVENT
BEGIN:VEVENT
CREATED:20130715T111654Z
LAST-MODIFIED:20130715T132556Z
DTSTAMP:20130715T132556Z
UID:1aef0b27-3d92-4581-829a-11999dd36724
SUMMARY:Werken
RECURRENCE-ID;TZID=Europe/Brussels:20130716T090000
DTSTART;TZID=Europe/Brussels:20130716T070000
DTEND;TZID=Europe/Brussels:20130716T150000
SEQUENCE:1
LOCATION:Job
X-MOZ-GENERATION:2
END:VEVENT
BEGIN:VEVENT
CREATED:20130715T125942Z
LAST-MODIFIED:20130715T130023Z
DTSTAMP:20130715T130023Z
UID:1aef0b27-3d92-4581-829a-11999dd36724
SUMMARY:Werken
RECURRENCE-ID;TZID=Europe/Brussels:20130717T090000
DTSTART;TZID=Europe/Brussels:20130717T070000
DTEND;TZID=Europe/Brussels:20130717T150000
SEQUENCE:1
LOCATION:Job
X-MOZ-GENERATION:3
END:VEVENT
BEGIN:VEVENT
CREATED:20130715T130024Z
LAST-MODIFIED:20130715T130034Z
DTSTAMP:20130715T130034Z
UID:1aef0b27-3d92-4581-829a-11999dd36724
SUMMARY:Werken
RECURRENCE-ID;TZID=Europe/Brussels:20130718T090000
DTSTART;TZID=Europe/Brussels:20130718T090000
DTEND;TZID=Europe/Brussels:20130718T170000
LOCATION:Job
X-MOZ-GENERATION:5
DESCRIPTION:Vrij
END:VEVENT
END:VCALENDAR
ICS;

        $vcal = Reader::read($input);
        $this->assertInstanceOf('Tine20\\VObject\\Component\\VCalendar', $vcal);

        $it = new Recur\EventIterator($vcal, '1aef0b27-3d92-4581-829a-11999dd36724');

        $result = array();
        foreach($it as $instance) {

            $result[] = $instance;

        }

        $tz = new DateTimeZone('Europe/Brussels');

        $this->assertEquals(array(
            new DateTime('2013-07-15 09:00:00', $tz),
            new DateTime('2013-07-16 07:00:00', $tz),
            new DateTime('2013-07-17 07:00:00', $tz),
            new DateTime('2013-07-18 09:00:00', $tz),
            new DateTime('2013-07-19 07:00:00', $tz),
        ), $result);

    }

}
