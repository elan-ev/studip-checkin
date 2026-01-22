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
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\JsonApi\Schemas\FormSchema;
use StudipCheckin\Models\Form;
use StudipCheckin\Models\FormUserData;
use StudipCheckin\Models\RelatedUser;

use UserFilter;

class Update extends JsonApiController
{
    use ValidationTrait;

    protected $allowedIncludePaths = [
        FormSchema::REL_FORM_USER_DATA,
        FormSchema::REL_RELATED_USERS,
        FormSchema::REL_USER_FILTER,
    ];

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
        $updatedForm->id = '';
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
        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'Missing `name` member of attributes block.';
        }
        if (!self::arrayHas($json, 'data.attributes.structure')) {
            return 'Missing `structure` member of attributes block.';
        }
        // Make sure structure is valid json or empty.
        $structure = self::arrayGet($json, 'data.attributes.structure', []);
        if (empty($structure)) {
            return '`structure` is required.';
        }

        $filterFields = self::arrayGet($json, 'data.attributes.filter-fields', null);
        // validating filter-fields if provided, to be array!
        if (!is_null($filterFields)) {
            if (!is_array($filterFields) || empty($filterFields)) {
                return 'Invalid `filter-fields` value.';
            }
        }

        if (!self::arrayHas($json, 'data.attributes.start-date')) {
            return 'Missing `start-date` member of attributes block.';
        }
        if (!self::arrayHas($json, 'data.attributes.end-date')) {
            return 'Missing `end-date` member of attributes block.';
        }
    }

    protected function updateCheckinForm($form, $json)
    {
        $name = self::arrayGet($json, 'data.attributes.name', '');
        $structure = self::arrayGet($json, 'data.attributes.structure', []);
        $startDate = self::arrayGet($json, 'data.attributes.start-date', 0);
        $endDate = self::arrayGet($json, 'data.attributes.end-date', 0);

        $filterFields = self::arrayGet($json, 'data.attributes.filter-fields', []);
        if (!empty($filterFields)) {
            $oldFilterId = $form->filter_id;
            $userFilter = new UserFilter($oldFilterId);
            $userFilter->fields = [];
            foreach ($filterFields as $field) {
                $classname = $field['attributes']['type'];
                $f = new $classname();
                if (!empty($field['id'])) {
                    $f->setId($field['id']);
                }
                $f->setCompareOperator($field['attributes']['compare-operator']);
                $f->setValue($field['attributes']['value']);
                $userFilter->addField($f);
            }
            $userFilter->store();

            $this->renewRelatedUserEntries($form, $userFilter->getUsers());
        }

        // In case structure has been changed, we increase the version.
        if ($this->compareStructures($form->structure->getArrayCopy(), $structure) === false) {
            $form->version += 1;
        }

        $form->name = $name;
        $form->structure = $structure;
        $form->start_date = $startDate;
        $form->end_date = $endDate;

        $form->store();

        return $form;
    }

    protected function compareStructures(array $oldStructure, array $newStructure): bool
    {
        ksort($oldStructure);
        ksort($newStructure);

        foreach ($oldStructure as $key => $value) {
            if (!array_key_exists($key, $newStructure) || $newStructure[$key] !== $value) {
                return false;
            }
        }

        return count($oldStructure) === count($newStructure);
    }

    protected function renewRelatedUserEntries($form, $newAffectedUserIds = [])
    {
        $currentUsersIds = [];
        // We go through all related users of the form and remove those that are no longer affected.
        foreach ($form->related_users as $relatedUser) {
            if (!in_array($relatedUser->user_id, $newAffectedUserIds)) {
                // TODO: We have to decide whether to remove associated form user data or not, currently not!
                /* // We first remove the form user data entries.
                $formUserDataEntries = FormUserData::findBySQL(
                    '`form_id` = ? AND `user_id` = ?',
                    [$form->id, $relatedUser->user_id]
                );
                foreach ($formUserDataEntries as $formUserData) {
                    $formUserData->delete();
                } */
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
            $relatedUser = new RelatedUser();
            $relatedUser->form_id = $form->id;
            $relatedUser->user_id = $userId;
            $relatedUser->store();
        }
    }
}
