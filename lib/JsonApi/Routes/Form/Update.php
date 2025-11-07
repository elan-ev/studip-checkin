<?php
/**
 * Form Update Handler
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Routes\Form;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\Form;

use UserFilter;

class Update extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);

        $user = $this->getUser($request);
        if (!Authority::canUpdateForm($user)) {
            throw new AuthorizationFailedException();
        }

        $form = Form::find($args['id']);
        if (!$form) {
            throw new RecordNotFoundException();
        }

        $updatedForm = $this->updateCheckinForm($form, $json);
        return $this->getContentResponse($updatedForm);
    }

    /**
     * @inheritDoc
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (!self::arrayHas($json, 'data.attributes')) {
            return 'Missing `attributes` member of data block.';
        }
        if (!self::arrayHas($json, 'data.attributes.filter-id')) {
            return 'Missing `filter-id` member of attributes block.';
        }
        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'Missing `name` member of attributes block.';
        }
        if (!self::arrayHas($json, 'data.attributes.structure')) {
            return 'Missing `structure` member of attributes block.';
        }
        // Make sure structure is valid json or empty.
        $structure = self::arrayGet($json, 'data.attributes.structure', '');
        $decoded = json_decode($structure, true);
        if (empty($structure) || empty($decoded)) {
            return 'Invalid `structure` value.';
        }
    }

    protected function updateCheckinForm($form, $json)
    {
        $filterId = self::arrayGet($json, 'data.attributes.filter-id', '');
        $name = self::arrayGet($json, 'data.attributes.name', '');
        $structure = self::arrayGet($json, 'data.attributes.structure', '');

        // We record this here, before we make any changes to the form object, in order to compare it later.
        $oldFilterId = $form->filter_id;

        // In case structure has been changed, we increase the version.
        if ($this->compareStructures($form->structure->getArrayCopy(), $structure) === false) {
            $form->version += 1;
        }

        $form->filter_id = $filterId;
        $form->name = $name;
        $form->structure = $structure;

        $form->store();

        // We renew and cleanup after the form instance is saved.
        if ($oldFilterId !== $filterId) {
            $this->renewRelatedUserEntries($form);

            // At this point we have no use for the old UserFilter anymore, so we remove it.
            $oldUserFilter = new UserFilter($oldFilterId);
            $oldUserFilter->delete();
        }

        // We have to empty out the id in order to return the JsonApi content response.
        $form->id = '';

        return $form;
    }

    protected function compareStructures($oldStructure, $newStructureStr): bool
    {
        ksort($oldStructure);
        $newStructure = json_decode($newStructureStr, true);
        ksort($newStructure);

        foreach ($oldStructure as $key => $value) {
            if (!array_key_exists($key, $newStructure) || $newStructure[$key] !== $value) {
                return false;
            }
        }

        return count($oldStructure) === count($newStructure);
    }

    protected function renewRelatedUserEntries($form)
    {
        // We get the new filter and compile the list of affected users.
        $userFilter = new UserFilter($form->filter_id);
        $newAffectedUserIds = $userFilter->getUsers();

        $currentUsersIds = [];
        // We go through all related users of the form and remove those that are no longer affected.
        foreach ($form->related_users as $relatedUser) {
            if (!in_array($relatedUser->user_id, $newAffectedUserIds)) {
                // We first remove the form user data entries.
                $formUserDataEntries = \StudipCheckin\Models\FormUserData::findBySQL('`form_id` = ? AND `user_id` = ?',
                    [$form->id, $relatedUser->user_id]);
                foreach ($formUserDataEntries as $formUserData) {
                    $formUserData->delete();
                }
                // Then we remove the related user entry.
                $relatedUser->delete();
            } else {
                // We record those users that are still affected.
                $currentUsersIds[] = $relatedUser->user_id;
            }
        }

        // We create RelatedUser entries for all newly affected users.
        foreach ($newAffectedUserIds as $userId) {
            if (in_array($userId, $currentUsersIds)) {
                continue;
            }
            $relatedUser = new \StudipCheckin\Models\RelatedUser();
            $relatedUser->form_id = $form->id;
            $relatedUser->user_id = $userId;
            $relatedUser->store();
        }
    }
}
