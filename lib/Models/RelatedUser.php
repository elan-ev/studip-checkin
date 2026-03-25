<?php

namespace StudipCheckin\Models;

use SimpleORMap;
use User;

/**
 * RelatedUser
 *
 * @package   StudipCheckin\Models
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 *
 * @property int $id
 * @property int $form_id
 * @property string $user_id
 * @property bool $active
 *
 * @property User $user belongs_to User
 */

class RelatedUser extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'checkin_related_users';

        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id',
        ];

        $config['has_one']['form'] = [
            'class_name'        => Form::class,
            'foreign_key'       => 'form_id',
            'assoc_foreign_key' => 'id',
        ];

        parent::configure($config);
    }

    public static function hasActiveRecord(string $userId): bool
    {
        return self::countBySQL('user_id = ? AND active = 1', [$userId]) > 0;
    }

    public static function hasAnyRecordByForm(string $userId, int $formId): bool
    {
        return self::countBySQL('user_id = ? AND form_id = ?', [$userId, $formId]) > 0;
    }

    public static function getActiveFormsByUser(string $userId): array
    {
        $records = self::findBySQL('user_id = ? AND active = 1', [$userId]);
        $forms = [];
        foreach ($records as $record) {
            $forms[] = $record->form;
        }
        return $forms;
    }

    public static function findActiveRecordByUserAndForm(string $userId, int $formId): ?self
    {
        return self::findOneBySQL(
            'user_id = ? AND form_id = ? AND active = 1',
            [$userId, $formId]
        );
    }
}
