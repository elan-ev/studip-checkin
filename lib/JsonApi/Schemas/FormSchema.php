<?php
/**
 * FormSchema
 *
 * JSON-API-Schema für das Form-Modell.
 *
 * @package   StudipCheckin\JsonApi\Schemas
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

use UserFilter;

class FormSchema extends \JsonApi\Schemas\SchemaProvider
{
    /**
     * Type of schema.
     * {@inheritdoc}
     */
    public const TYPE = 'checkin-forms';

    /**
     * Resource Type.
     * {@inheritdoc}
     */
    protected $resourceType = self::TYPE;

    // Here comes relationship constants if any.
    const REL_USER_FILTER = 'user-filter';
    const REL_RELATED_USERS = 'related-users';
    const REL_FORM_USER_DATA = 'form-user-data';

    /**
     * {@inheritdoc}
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'filter_id'  => (string) $resource['filter_id'],
            'name' => (string) $resource['name'],
            'structure' => (string) $resource['structure'],
            'version' => (string) $resource['version'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $userFilter = new UserFilter($resource['filter_id']);
        $relationships[self::REL_USER_FILTER] = $userFilter
            ? [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($userFilter),
                ],
                self::RELATIONSHIP_DATA => $userFilter,
            ]
            : [self::RELATIONSHIP_DATA => null];

        $relatedUsers = $resource->related_users;
        $relationships[self::REL_RELATED_USERS] = $relatedUsers
            ? [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getFactory()->createLink(
                        true,
                        $this->getSelfSubUrl($resource) . '/' . self::REL_RELATED_USERS,
                        true,
                        []
                    ),
                ],
                self::RELATIONSHIP_DATA => $relatedUsers,
            ]
            : [self::RELATIONSHIP_DATA => null];

        $formUserData = $resource->form_user_data;
        $relationships[self::REL_FORM_USER_DATA] = $formUserData
            ? [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getFactory()->createLink(
                        true,
                        $this->getSelfSubUrl($resource) . '/' . self::REL_FORM_USER_DATA,
                        true,
                        []
                    ),
                ],
                self::RELATIONSHIP_DATA => $formUserData,
            ]
            : [self::RELATIONSHIP_DATA => null];

        return $relationships;
    }
}
