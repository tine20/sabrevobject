<?php

namespace Tine20\VObject;

class Issue64Test extends \PHPUnit_Framework_TestCase {

    function testRead() {

        $vcard = Reader::read(file_get_contents(dirname(__FILE__) . '/issue64.vcf'));
        $vcard = $vcard->convert(\Tine20\VObject\Document::VCARD30);
        $vcard = $vcard->serialize();

        $converted = Reader::read($vcard);

        $this->assertInstanceOf('Tine20\\VObject\\Component\\VCard', $converted);

    }

}
