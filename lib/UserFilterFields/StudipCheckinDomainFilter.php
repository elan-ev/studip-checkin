<?php
/**
 * Domain Condition Field for StudipCheckin Plugin
 *
 * @package   UserFilterFields\StudipCheckin
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */

namespace UserFilterFields\StudipCheckin;

use UserFilterFields\DomainCondition;

class StudipCheckinDomainFilter extends DomainCondition
{

    /**
     * @inheritDoc
     */
    public string $target = '';

    /**
     * @inheritDoc
     */
    public function getUsers($restrictions = [])
    {
        return parent::getUsers($restrictions);
    }
}
