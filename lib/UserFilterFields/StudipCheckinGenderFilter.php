<?php
/**
 * Gender Condition Field for StudipCheckin Plugin
 *
 * @package   UserFilterFields\StudipCheckin
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace UserFilterFields\StudipCheckin;

use UserFilterField;
use DBManager;

class StudipCheckinGenderFilter extends UserFilterField
{
    public $userDataDbField = 'geschlecht';
    public $userDataDbTable = 'user_info';

    public static $sortOrder = 8;

    public $target = '';

    /**
     * @inheritDoc
     */
    public function __construct($fieldId = '')
    {
        parent::__construct($fieldId);

        $this->validCompareOperators = [
            '='   => _('ist'),
            '!=' => _('ist nicht'),
        ];

        $this->validValues = [
            0 => _('unbekannt'),
            1 => _('männlich'),
            2 => _('weiblich'),
            3 => _('divers')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return _('Geschlecht');
    }

    /**
     * @inheritDoc
     */
    public function getUsers($restrictions = [])
    {
        return DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` " .
                "FROM `" . $this->userDataDbTable . "` " .
                "WHERE `" . $this->userDataDbField . "`" . $this->compareOperator .
                "?", [$this->value]);
    }
}
