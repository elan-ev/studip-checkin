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
            'structure' => $resource['structure']->getArrayCopy(),
            'version' => (string) $resource['version'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addUserFilterRelationship(
            $relationships,
            $resource,
            $this->shouldInclude($context, self::REL_USER_FILTER)
        );

        $relationships = $this->addRelatedUsersRelationship(
            $relationships,
            $resource,
            $this->shouldInclude($context, self::REL_RELATED_USERS)
        );

        $relationships = $this->addFormUserDataRelationship(
            $relationships,
            $resource,
            $this->shouldInclude($context, self::REL_FORM_USER_DATA)
        );

        return $relationships;
    }

    private function addUserFilterRelationship(array $relationships, $resource, bool $includeData): array
    {
        $userFilter = new UserFilter($resource['filter_id']);
        $relation = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($userFilter),
            ],
        ];

        if ($includeData) {
            $relation[self::RELATIONSHIP_DATA] = $userFilter;
        }

        $relationships[self::REL_USER_FILTER] = $relation;

        return $relationships;
    }

    private function addRelatedUsersRelationship(array $relationships, $resource, bool $includeData): array
    {
        $relatedUsers = $resource->related_users;
        $relation = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getFactory()->createLink(
                    true,
                    $this->getSelfSubUrl($resource) . '/' . self::REL_RELATED_USERS,
                    true,
                    []
                ),
            ],
        ];

        if ($includeData) {
            $relation[self::RELATIONSHIP_DATA] = $relatedUsers;
        }

        $relationships[self::REL_RELATED_USERS] = $relation;

        return $relationships;
    }

    private function addFormUserDataRelationship(array $relationships, $resource, bool $includeData): array
    {
        $formUserData = $resource->form_user_data;
        $relation = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getFactory()->createLink(
                    true,
                    $this->getSelfSubUrl($resource) . '/' . self::REL_FORM_USER_DATA,
                    true,
                    []
                ),
            ],
        ];

        if ($includeData) {
            $relation[self::RELATIONSHIP_DATA] = $formUserData;
        }

        $relationships[self::REL_FORM_USER_DATA] = $relation;

        return $relationships;
    }
}
