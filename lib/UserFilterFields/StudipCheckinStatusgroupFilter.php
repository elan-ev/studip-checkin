<?php
/**
 * Statusgroup Condition Field for StudipCheckin Plugin
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

class StudipCheckinStatusgroupFilter extends UserFilterField
{
    public $valuesDbTable = 'statusgruppen';
    public $valuesDbIdField = 'statusgruppe_id';
    public $valuesDbNameField = 'name';
    public $userDataDbTable = 'statusgruppe_user';
    public $userDataDbField = 'statusgruppe_id';

    public static $sortOrder = 10;

    /**
     * @inheritDoc
     */
    public static function getTargets()
    {
        return ['employees'];
    }

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

        $this->validValues = DBManager::get()->fetchFirst(
            "SELECT DISTINCT `name` FROM `statusgruppen` ORDER BY `name` ASC"
        );
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return _('Statusgruppe');
    }

    /**
     * @inheritDoc
     */
    public function getUsers($restrictions = [])
    {
        return DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` " .
            "FROM `" . $this->userDataDbTable . "` " .
            "WHERE `" . $this->userDataDbField . "`" . $this->compareOperator .
            "?", [$this->value]
        );
    }
}
