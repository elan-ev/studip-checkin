<?php
/**
 * Degree Condition Field for StudipCheckin Plugin
 *
 * @package   UserFilterFields\StudipCheckin
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace UserFilterFields\StudipCheckin;

use UserFilterFields\DegreeCondition;

class StudipCheckinDegreeFilter extends DegreeCondition
{
    /**
     * @inheritDoc
     */
    public static function getTargets()
    {
        return ['students'];
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
    public function getUsers($restrictions = [])
    {
        return parent::getUsers($restrictions);
    }

    /**
     * @inheritDoc
     */
    public function getUserValues($userId, $additional = null)
    {
        return parent::getUserValues($userId, $additional);
    }
}
