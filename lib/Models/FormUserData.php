<?php

namespace StudipCheckin\Models;

use SimpleORMap;
use JSONArrayObject;
use User;

/**
 * FormUserData
 *
 * @package   StudipCheckin\Models
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 *
 * @property int $id
 * @property int $form_id
 * @property string $user_id
 * @property bool $active
 * @property \JSONArrayObject $form_data database column
 * @property int $form_version
 *
 * @property User $user belongs_to User
 */

class FormUserData extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'checkin_form_user_data';

        $config['serialized_fields']['form_data'] = JSONArrayObject::class;

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

    public static function findByUserFormVersion(int $formId, string $userId, int $formVersion): ?self
    {
        return self::findOneBySQL(
            'form_id = ? AND user_id = ? AND form_version = ?',
            [$formId, $userId, $formVersion]
        );
    }
}
