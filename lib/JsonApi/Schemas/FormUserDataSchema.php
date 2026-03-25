<?php
/**
 * FormUserDataSchema
 *
 * JSON-API-Schema für das FormUserData-Modell.
 *
 * @package   StudipCheckin\JsonApi\Schemas
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class FormUserDataSchema extends \JsonApi\Schemas\SchemaProvider
{
    /**
     * Type of schema.
     * {@inheritdoc}
     */
    public const TYPE = 'checkin-form-user-data';

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
            'form-id'  => (int) $resource['form_id'],
            'data' => $resource['form_data']->getArrayCopy(),
            'version' => (int) $resource['form_version'],
            'mkdate' => $resource['mkdate'] ? date('d.m.Y H:i', $resource['mkdate']) : null,
            'chdate' => $resource['chdate'] ? date('d.m.Y H:i', $resource['chdate']) : null,
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

    /**
     * @inheritdoc
     */
    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceMeta($resource)
    {
        $userMeta = [];
        if ($resource?->user) {
            $userMeta = [
                'name' => $resource->user->vorname,
                'surename' => $resource->user->nachname,
                'username' => $resource->user->username,
                'email' => $resource->user->email,
                'fullname' => $resource->user->getFullname(),
            ];
        }
        return [
            'user' => $userMeta,
        ];
    }
}
