<?php
/**
 * RelatedUserSchema
 *
 * JSON-API-Schema für das RelatedUser-Modell.
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

class RelatedUserSchema extends \JsonApi\Schemas\SchemaProvider
{
    /**
     * Type of schema.
     * {@inheritdoc}
     */
    public const TYPE = 'checkin-related-users';

    /**
     * Resource Type.
     * {@inheritdoc}
     */
    protected $resourceType = self::TYPE;

    // Here comes relationship constants if any.
    const REL_USER = 'user';
    const REL_FORM = 'form';

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
            'form_id'  => (int) $resource['form_id'],
            'user_id' => (string) $resource['user_id'],
            'active' => (bool) $resource['active'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $user = $resource->user;
        $relationships[self::REL_USER] = $user
            ? [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($user),
                ],
                self::RELATIONSHIP_DATA => $user,
            ]
            : [self::RELATIONSHIP_DATA => null];

        $form = $resource->form;
        $relationships[self::REL_FORM] = $form
            ? [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($form),
                ],
                self::RELATIONSHIP_DATA => $form,
            ]
            : [self::RELATIONSHIP_DATA => null];

        return $relationships;
    }
}
