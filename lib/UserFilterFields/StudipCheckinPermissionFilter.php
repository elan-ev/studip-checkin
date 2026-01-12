<?php
/**
 * Permission Condition Field for StudipCheckin Plugin
 *
 * @package   UserFilterFields\StudipCheckin
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace UserFilterFields\StudipCheckin;

use UserFilterFields\PermissionCondition;
use DBManager;

class MassMailPermissionFilter extends PermissionCondition
{

    public string $target = '';

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
        $this->userDataDbTable = 'auth_user_md5';
        $this->userDataDbField = 'perms';

        parent::__construct($fieldId);

        $this->validValues = [
            'autor' => _('Student/in'),
            'tutor' => _('Tutor/in'),
            'dozent' => _('Lehrende/r')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return _('Globaler Status');
    }

    /**
     * @inheritDoc
     */
    public function getUsers($restrictions = array())
    {
        return DBManager::get()->fetchFirst("SELECT DISTINCT `user_id` " .
            "FROM `" . $this->userDataDbTable . "` " .
            "WHERE `" . $this->userDataDbField . "`" . $this->compareOperator .
            "?", [$this->value]
        );
    }
}
