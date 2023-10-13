<?php

namespace Tine20\VObject\Property;

use Tine20\VObject;

class BinaryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \InvalidArgumentException
     */
    function testMimeDir() {

        $vcard = new VObject\Component\VCard();
        $vcard->add('PHOTO', array('a','b'));

    }

}
