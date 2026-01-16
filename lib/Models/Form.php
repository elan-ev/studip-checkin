<?php

namespace StudipCheckin\Models;

use SimpleORMap;
use JSONArrayObject;

/**
 * Form
 *
 * @package   StudipCheckin\Models
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 *
 * @property int $id
 * @property string $filter_id
 * @property string $name
 * @property \JSONArrayObject $structure database column
 * @property int $version
 */

class Form extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'checkin_forms';

        $config['has_many']['related_users'] = [
            'class_name'        => RelatedUser::class,
            'assoc_foreign_key' => 'form_id',
            'on_delete'         => 'delete',
        ];

        $config['has_many']['form_user_data'] = [
            'class_name'        => FormUserData::class,
            'assoc_foreign_key' => 'form_id',
            'on_delete'         => 'delete',
        ];

        $config['serialized_fields']['structure'] = JSONArrayObject::class;

        parent::configure($config);
    }

    public static function getAll(): array
    {
        return self::findBySQL('1');
    }
}
