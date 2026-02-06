<?php
/**
 * Institution Condition Field for StudipCheckin Plugin
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

class StudipCheckinInstituteFilter extends UserFilterField
{
    public $valuesDbTable = 'Institute';
    public $valuesDbIdField = 'Institut_id';
    public $valuesDbNameField = 'Name';
    public $userDataDbTable = 'user_inst';
    public $userDataDbField = 'Institut_id';

    public static $sortOrder = 9;

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
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return _('Einrichtung');
    }

    /**
     * @inheritDoc
     */
    public function getUsers($restrictions = [])
    {
        return DBManager::get()->fetchFirst(
            "SELECT DISTINCT `user_id` " .
            "FROM `" . $this->userDataDbTable . "` " .
            "WHERE `" . $this->userDataDbField . "`" . $this->compareOperator .
            ":value AND `inst_perms` IN (:perms)", ['value' => $this->value,
            'perms' => ['autor', 'tutor', 'dozent', 'admin']]
        );
    }
}
