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
 * @license   AGPL-3.0
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
}
