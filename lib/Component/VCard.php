<?php

namespace Tine20\VObject\Component;

use
    Tine20\VObject;

/**
 * The VCard component
 *
 * This component represents the BEGIN:VCARD and END:VCARD found in every
 * vcard.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class VCard extends VObject\Document {

    /**
     * The default name for this component.
     *
     * This should be 'VCALENDAR' or 'VCARD'.
     *
     * @var string
     */
    static $defaultName = 'VCARD';

    /**
     * Caching the version number
     *
     * @var int
     */
    private $version = null;

    /**
     * List of value-types, and which classes they map to.
     *
     * @var array
     */
    static $valueMap = array(
        'BINARY'           => 'Tine20\\VObject\\Property\\Binary',
        'BOOLEAN'          => 'Tine20\\VObject\\Property\\Boolean',
        'CONTENT-ID'       => 'Tine20\\VObject\\Property\\FlatText',   // vCard 2.1 only
        'DATE'             => 'Tine20\\VObject\\Property\\VCard\\Date',
        'DATE-TIME'        => 'Tine20\\VObject\\Property\\VCard\\DateTime',
        'DATE-AND-OR-TIME' => 'Tine20\\VObject\\Property\\VCard\\DateAndOrTime', // vCard only
        'FLOAT'            => 'Tine20\\VObject\\Property\\FloatValue',
        'INTEGER'          => 'Tine20\\VObject\\Property\\IntegerValue',
        'LANGUAGE-TAG'     => 'Tine20\\VObject\\Property\\VCard\\LanguageTag',
        'TIMESTAMP'        => 'Tine20\\VObject\\Property\\VCard\\TimeStamp',
        'TEXT'             => 'Tine20\\VObject\\Property\\Text',
        'TIME'             => 'Tine20\\VObject\\Property\\Time',
        'UNKNOWN'          => 'Tine20\\VObject\\Property\\Unknown', // jCard / jCal-only.
        'URI'              => 'Tine20\\VObject\\Property\\Uri',
        'URL'              => 'Tine20\\VObject\\Property\\Uri', // vCard 2.1 only
        'UTC-OFFSET'       => 'Tine20\\VObject\\Property\\UtcOffset',
    );

    /**
     * List of properties, and which classes they map to.
     *
     * @var array
     */
    static $propertyMap = array(

        // vCard 2.1 properties and up
        'N'       => 'Tine20\\VObject\\Property\\Text',
        'FN'      => 'Tine20\\VObject\\Property\\FlatText',
        'PHOTO'   => 'Tine20\\VObject\\Property\\Binary', // Todo: we should add a class for Binary values.
        'BDAY'    => 'Tine20\\VObject\\Property\\VCard\\DateAndOrTime',
        'ADR'     => 'Tine20\\VObject\\Property\\Text',
        'LABEL'   => 'Tine20\\VObject\\Property\\FlatText', // Removed in vCard 4.0
        'TEL'     => 'Tine20\\VObject\\Property\\FlatText',
        'EMAIL'   => 'Tine20\\VObject\\Property\\FlatText',
        'MAILER'  => 'Tine20\\VObject\\Property\\FlatText', // Removed in vCard 4.0
        'GEO'     => 'Tine20\\VObject\\Property\\FlatText',
        'TITLE'   => 'Tine20\\VObject\\Property\\FlatText',
        'ROLE'    => 'Tine20\\VObject\\Property\\FlatText',
        'LOGO'    => 'Tine20\\VObject\\Property\\Binary',
        // 'AGENT'   => 'Tine20\\VObject\\Property\\',      // Todo: is an embedded vCard. Probably rare, so
                                 // not supported at the moment
        'ORG'     => 'Tine20\\VObject\\Property\\Text',
        'NOTE'    => 'Tine20\\VObject\\Property\\FlatText',
        'REV'     => 'Tine20\\VObject\\Property\\VCard\\TimeStamp',
        'SOUND'   => 'Tine20\\VObject\\Property\\FlatText',
        'URL'     => 'Tine20\\VObject\\Property\\Uri',
        'UID'     => 'Tine20\\VObject\\Property\\FlatText',
        'VERSION' => 'Tine20\\VObject\\Property\\FlatText',
        'KEY'     => 'Tine20\\VObject\\Property\\FlatText',
        'TZ'      => 'Tine20\\VObject\\Property\\Text',

        // vCard 3.0 properties
        'CATEGORIES'  => 'Tine20\\VObject\\Property\\Text',
        'SORT-STRING' => 'Tine20\\VObject\\Property\\FlatText',
        'PRODID'      => 'Tine20\\VObject\\Property\\FlatText',
        'NICKNAME'    => 'Tine20\\VObject\\Property\\Text',
        'CLASS'       => 'Tine20\\VObject\\Property\\FlatText', // Removed in vCard 4.0

        // rfc2739 properties
        'FBURL'        => 'Tine20\\VObject\\Property\\Uri',
        'CAPURI'       => 'Tine20\\VObject\\Property\\Uri',
        'CALURI'       => 'Tine20\\VObject\\Property\\Uri',

        // rfc4770 properties
        'IMPP'         => 'Tine20\\VObject\\Property\\Uri',

        // vCard 4.0 properties
        'XML'          => 'Tine20\\VObject\\Property\\FlatText',
        'ANNIVERSARY'  => 'Tine20\\VObject\\Property\\VCard\\DateAndOrTime',
        'CLIENTPIDMAP' => 'Tine20\\VObject\\Property\\Text',
        'LANG'         => 'Tine20\\VObject\\Property\\VCard\\LanguageTag',
        'GENDER'       => 'Tine20\\VObject\\Property\\Text',
        'KIND'         => 'Tine20\\VObject\\Property\\FlatText',

        // rfc6474 properties
        'BIRTHPLACE'    => 'Tine20\\VObject\\Property\\FlatText',
        'DEATHPLACE'    => 'Tine20\\VObject\\Property\\FlatText',
        'DEATHDATE'     => 'Tine20\\VObject\\Property\\VCard\\DateAndOrTime',

        // rfc6715 properties
        'EXPERTISE'     => 'Tine20\\VObject\\Property\\FlatText',
        'HOBBY'         => 'Tine20\\VObject\\Property\\FlatText',
        'INTEREST'      => 'Tine20\\VObject\\Property\\FlatText',
        'ORG-DIRECTORY' => 'Tine20\\VObject\\Property\\FlatText',

    );

    /**
     * Returns the current document type.
     *
     * @return void
     */
    function getDocumentType() {

        if (!$this->version) {
            $version = (string)$this->VERSION;
            switch($version) {
                case '2.1' :
                    $this->version = self::VCARD21;
                    break;
                case '3.0' :
                    $this->version = self::VCARD30;
                    break;
                case '4.0' :
                    $this->version = self::VCARD40;
                    break;
                default :
                    $this->version = self::UNKNOWN;
                    break;

            }
        }

        return $this->version;

    }

    /**
     * Converts the document to a different vcard version.
     *
     * Use one of the VCARD constants for the target. This method will return
     * a copy of the vcard in the new version.
     *
     * At the moment the only supported conversion is from 3.0 to 4.0.
     *
     * If input and output version are identical, a clone is returned.
     *
     * @param int $target
     * @return VCard
     */
    function convert($target) {

        $converter = new VObject\VCardConverter();
        return $converter->convert($this, $target);

    }

    /**
     * VCards with version 2.1, 3.0 and 4.0 are found.
     *
     * If the VCARD doesn't know its version, 2.1 is assumed.
     */
    const DEFAULT_VERSION = self::VCARD21;

    /**
     * Validates the node for correctness.
     *
     * The following options are supported:
     *   Node::REPAIR - May attempt to automatically repair the problem.
     *
     * This method returns an array with detected problems.
     * Every element has the following properties:
     *
     *  * level - problem level.
     *  * message - A human-readable string describing the issue.
     *  * node - A reference to the problematic node.
     *
     * The level means:
     *   1 - The issue was repaired (only happens if REPAIR was turned on)
     *   2 - An inconsequential issue
     *   3 - A severe issue.
     *
     * @param int $options
     * @return array
     */
    function validate($options = 0) {

        $warnings = array();

        $versionMap = array(
            self::VCARD21 => '2.1',
            self::VCARD30 => '3.0',
            self::VCARD40 => '4.0',
        );

        $version = $this->select('VERSION');
        if (count($version)===1) {
            $version = (string)$this->VERSION;
            if ($version!=='2.1' && $version!=='3.0' && $version!=='4.0') {
                $warnings[] = array(
                    'level' => 3,
                    'message' => 'Only vcard version 4.0 (RFC6350), version 3.0 (RFC2426) or version 2.1 (icm-vcard-2.1) are supported.',
                    'node' => $this,
                );
                if ($options & self::REPAIR) {
                    $this->VERSION = $versionMap[self::DEFAULT_VERSION];
                }
            }
            if ($version === '2.1' && ($options & self::PROFILE_CARDDAV)) {
                $warnings[] = array(
                    'level' => 3,
                    'message' => 'CardDAV servers are not allowed to accept vCard 2.1.',
                    'node' => $this,
                );
            }

        }
        $uid = $this->select('UID');
        if (count($uid) === 0) {
            if ($options & self::PROFILE_CARDDAV) {
                // Required for CardDAV
                $warningLevel = 3;
                $message = 'vCards on CardDAV servers MUST have a UID property.';
            } else {
                // Not required for regular vcards
                $warningLevel = 2;
                $message = 'Adding a UID to a vCard property is recommended.';
            }
            if ($options & self::REPAIR) {
                $this->UID = VObject\UUIDUtil::getUUID();
                $warningLevel = 1;
            }
            $warnings[] = array(
                'level' => $warningLevel,
                'message' => $message,
                'node' => $this,
            );
        }

        $fn = $this->select('FN');
        if (count($fn)!==1) {

            $repaired = false;
            if (($options & self::REPAIR) && count($fn) === 0) {
                // We're going to try to see if we can use the contents of the
                // N property.
                if (isset($this->N)) {
                    $value = explode(';', (string)$this->N);
                    if (isset($value[1]) && $value[1]) {
                        $this->FN = $value[1] . ' ' . $value[0];
                    } else {
                        $this->FN = $value[0];
                    }
                    $repaired = true;

                // Otherwise, the ORG property may work
                } elseif (isset($this->ORG)) {
                    $this->FN = (string)$this->ORG;
                    $repaired = true;
                }

            }
            $warnings[] = array(
                'level' => $repaired?1:3,
                'message' => 'The FN property must appear in the VCARD component exactly 1 time',
                'node' => $this,
            );
        }

        return array_merge(
            parent::validate($options),
            $warnings
        );

    }

    /**
     * A simple list of validation rules.
     *
     * This is simply a list of properties, and how many times they either
     * must or must not appear.
     *
     * Possible values per property:
     *   * 0 - Must not appear.
     *   * 1 - Must appear exactly once.
     *   * + - Must appear at least once.
     *   * * - Can appear any number of times.
     *   * ? - May appear, but not more than once.
     *
     * @var array
     */
    function getValidationRules() {

        return array(
            'ADR'          => '*',
            'ANNIVERSARY'  => '?',
            'BDAY'         => '?',
            'CALADRURI'    => '*',
            'CALURI'       => '*',
            'CATEGORIES'   => '*',
            'CLIENTPIDMAP' => '*',
            'EMAIL'        => '*',
            'FBURL'        => '*',
            'IMPP'         => '*',
            'GENDER'       => '?',
            'GEO'          => '*',
            'KEY'          => '*',
            'KIND'         => '?',
            'LANG'         => '*',
            'LOGO'         => '*',
            'MEMBER'       => '*',
            'N'            => '?',
            'NICKNAME'     => '*',
            'NOTE'         => '*',
            'ORG'          => '*',
            'PHOTO'        => '*',
            'PRODID'       => '?',
            'RELATED'      => '*',
            'REV'          => '?',
            'ROLE'         => '*',
            'SOUND'        => '*',
            'SOURCE'       => '*',
            'TEL'          => '*',
            'TITLE'        => '*',
            'TZ'           => '*',
            'URL'          => '*',
            'VERSION'      => '1',
            'XML'          => '*',

            // FN is commented out, because it's already handled by the
            // validate function, which may also try to repair it.
            // 'FN'           => '+',

            'UID'          => '?',
        );

    }

    /**
     * Returns a preferred field.
     *
     * VCards can indicate wether a field such as ADR, TEL or EMAIL is
     * preferred by specifying TYPE=PREF (vcard 2.1, 3) or PREF=x (vcard 4, x
     * being a number between 1 and 100).
     *
     * If neither of those parameters are specified, the first is returned, if
     * a field with that name does not exist, null is returned.
     *
     * @param string $fieldName
     * @return VObject\Property|null
     */
    function preferred($propertyName) {

        $preferred = null;
        $lastPref = 101;
        foreach($this->select($propertyName) as $field) {

            $pref = 101;
            if (isset($field['TYPE']) && $field['TYPE']->has('PREF')) {
                $pref = 1;
            } elseif (isset($field['PREF'])) {
                $pref = $field['PREF']->getValue();
            }

            if ($pref < $lastPref || is_null($preferred)) {
                $preferred = $field;
                $lastPref = $pref;
            }

        }
        return $preferred;

    }

    /**
     * This method should return a list of default property values.
     *
     * @return array
     */
    protected function getDefaults() {

        return array(
            'VERSION' => '3.0',
            'PRODID' => '-//Sabre//Sabre VObject ' . VObject\Version::VERSION . '//EN',
        );

    }

    /**
     * This method returns an array, with the representation as it should be
     * encoded in json. This is used to create jCard or jCal documents.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    function jsonSerialize() {

        // A vcard does not have sub-components, so we're overriding this
        // method to remove that array element.
        $properties = array();

        foreach($this->children as $child) {
            $properties[] = $child->jsonSerialize();
        }

        return array(
            strtolower($this->name),
            $properties,
        );

    }

    /**
     * Returns the default class for a property name.
     *
     * @param string $propertyName
     * @return string
     */
    function getClassNameForPropertyName($propertyName) {

        $className = parent::getClassNameForPropertyName($propertyName);
        // In vCard 4, BINARY no longer exists, and we need URI instead.

        if ($className == 'Tine20\\VObject\\Property\\Binary' && $this->getDocumentType()===self::VCARD40) {
            return 'Tine20\\VObject\\Property\\Uri';
        }
        return $className;

    }

}

