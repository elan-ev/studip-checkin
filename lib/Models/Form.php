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
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 *
 * @property int $id
 * @property string $filter_id
 * @property \JSONArrayObject $name
 * @property \JSONArrayObject $description
 * @property \JSONArrayObject $structure database column
 * @property int $version
 * @property int $start_date
 * @property int $end_date
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

        $config['serialized_fields']['name'] = JSONArrayObject::class;
        $config['serialized_fields']['description'] = JSONArrayObject::class;
        $config['serialized_fields']['structure'] = JSONArrayObject::class;
        

        parent::configure($config);
    }

    public function isRunnable(): bool
    {
        $now = time();
        // Has Start Date only.
        if (!empty($this->start_date) && empty($this->end_date)) {
            return (int) $this->start_date <= $now;
        }

        // Has End Date only.
        if (empty($this->start_date) && !empty($this->end_date)) {
            return (int) $this->end_date >= $now;
        }

        // Has both dates.
        if (!empty($this->start_date) && !empty($this->end_date)) {
            return
                (int) $this->start_date <= $now &&
                (int) $this->end_date >= $now;
        }

        // If no date applied!
        return true;
    }

    public static function getAll(): array
    {
        return self::findBySQL('1');
    }

    public static function refineStructure($structure): array
    {
        // First we populate the default ids.
        $refinedStructure = array_map(function ($element) {
            if (!str_starts_with($element['id'], 'elm')) {
                $element['id'] = uniqid('elm');
            }
            return $element;
        }, $structure);

        // Then we remove those items that does not have proper data (empty items), if any.
        $filteredStructure = array_filter($refinedStructure, function ($element) {
            $isValid = !empty($element['payload']['label']);
            if ($isValid && in_array($element['type'], ['select', 'radio'])) {
                $isValid = !empty($element['payload']['options']);
            }
            return $isValid;
        });

        return $filteredStructure;
    }
}
