<?php

namespace Tine20\VObject\Property\ICalendar;

class CalAddressTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider values
     */
    function testGetNormalizedValue($expected, $input) {

        $vobj = new \Tine20\VObject\Component\VCalendar();
        $property = $vobj->add('ATTENDEE', $input);

        $this->assertEquals(
            $expected,
            $property->getNormalizedValue()
        );

    }

    function values() {

        return array(
            array('mailto:a@b.com', 'mailto:a@b.com'),
            array('mailto:a@b.com', 'MAILTO:a@b.com'),
            array('/foo/bar', '/foo/bar'),
        );

    }

}
