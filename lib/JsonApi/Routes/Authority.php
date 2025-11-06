<?php
/**
 * Authority
 *
 * Main class to handle action validity checks.
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Routes;

use User;

class Authority
{
    public static function canIndexForm(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id);
    }

    public static function canCreateForm(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id);
    }

    public static function canUpdateForm(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id);
    }

    public static function canDeleteForm(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id);
    }

    public static function canViewForm(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('admin', $user->id);
    }
}
