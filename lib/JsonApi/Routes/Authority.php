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
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Routes;

use User;
use RolePersistence;

class Authority
{
    const ROLENAME = 'CheckinPlugin_Admin';

    private static function rootAndAdminOnly(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('root', $user->id) || RolePersistence::isAssignedRole($user->id, self::ROLENAME);
    }

    public static function canIndexForm(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canCreateForm(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canUpdateForm(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canDeleteForm(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canShowForm(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canIndexRelatedUsers(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canShowRelatedUser(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canDeleteRelatedUser(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canCreateRelatedUser(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canUpdateRelatedUser(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canIndexUserFilterFields(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canCreateUserFilters(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canCreateFormUserData(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('user', $user->id);
    }

    public static function canIndexUserForms(User $user, User $requesting): bool
    {
        return $GLOBALS['perm']->have_perm('user', $user->id) && $user->id === $requesting->id;
    }

    public static function canDeleteFormUserData(User $user, User $requesting): bool
    {
        if (self::rootAndAdminOnly($requesting)) {
            return true;
        }

        return $GLOBALS['perm']->have_perm('user', $user->id) && $user->id === $requesting->id;
    }

    public static function canIndexFormUserData(User $user): bool
    {
        return self::rootAndAdminOnly($user);
    }

    public static function canIndexUsersFormUserData(User $user, User $requesting): bool
    {
        if (self::rootAndAdminOnly($requesting)) {
            return true;
        }

        return $GLOBALS['perm']->have_perm('user', $user->id) && $user->id === $requesting->id;
    }

    public static function canShowFormUserData(User $user, User $requesting): bool
    {
        if (self::rootAndAdminOnly($requesting)) {
            return true;
        }

        return $GLOBALS['perm']->have_perm('user', $user->id) && $user->id === $requesting->id;
    }

    public static function canUpdateFormUserData(User $user, User $requesting): bool
    {
        if (self::rootAndAdminOnly($requesting)) {
            return true;
        }

        return $GLOBALS['perm']->have_perm('user', $user->id) && $user->id === $requesting->id;
    }
}
