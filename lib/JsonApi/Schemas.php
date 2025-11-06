<?php
/**
 * Schemas
 *
 * Trait zum Registrieren von JSON-API-Schemata für StudipCheckin-Modelle.
 *
 * @package   StudipCheckin\JsonApi
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi;

trait Schemas
{
    public function registerSchemas(): array
    {
        return [
            \StudipCheckin\Models\Form::class => \StudipCheckin\JsonApi\Schemas\FormSchema::class,
            \StudipCheckin\Models\FormUserData::class => \StudipCheckin\JsonApi\Schemas\FormUserDataSchema::class,
            \StudipCheckin\Models\RelatedUser::class => \StudipCheckin\JsonApi\Schemas\RelatedUserSchema::class,
        ];
    }
}
