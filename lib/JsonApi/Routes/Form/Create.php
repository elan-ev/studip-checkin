<?php
/**
 * Form Create Handler
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
use JsonApi\Errors\ConflictException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\JsonApi\Schemas\FormSchema;
use StudipCheckin\Models\Form;

use UserFilter;

class Create extends JsonApiController
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

        if (!Authority::canCreateForm($user)) {
            throw new AuthorizationFailedException();
        }

        $form = $this->createCheckinForm($json);

        return $this->getCreatedResponse($form);
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
        // Make sure structure is valid json or not empty.
        $structure = self::arrayGet($json, 'data.attributes.structure', []);
        if (empty($structure)) {
            return '`structure` is required.';
        }

        // We use filter fields instead of filter id!
        if (!self::arrayHas($json, 'data.attributes.filter-fields')) {
            return 'Missing `filter-fields` member of attributes block.';
        }
        // Make sure filter fields is not empty.
        $fields = self::arrayGet($json, 'data.attributes.filter-fields', null);
        if (empty($fields)) {
            return '`filter-fields` are required.';
        }

        if (!self::arrayHas($json, 'data.attributes.start-date')) {
            return 'Missing `start-date` member of attributes block.';
        }
        if (!self::arrayHas($json, 'data.attributes.end-date')) {
            return 'Missing `end-date` member of attributes block.';
        }
    }

    private function createCheckinForm($json)
    {
        $name = self::arrayGet($json, 'data.attributes.name', '');
        $structure = self::arrayGet($json, 'data.attributes.structure', []);
        $startDate = self::arrayGet($json, 'data.attributes.start-date', '');
        $endDate = self::arrayGet($json, 'data.attributes.end-date', '');
        $description = self::arrayGet($json, 'data.attributes.description', '');

        // Here we then get the filter and compile the list of affected users.
        $filterFields = self::arrayGet($json, 'data.attributes.filter-fields', []);
        $userFilter = new UserFilter();
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

        $form = new Form();
        $form->filter_id = $userFilter->getId();
        $form->name = $name;
        $form->description = $description;
        $form->structure = $structure;
        $form->version = 1;
        $form->start_date = !empty($startDate) ? strtotime($startDate) : 0;
        $form->end_date = !empty($endDate) ? strtotime($endDate) : 0;
        $form->store();

        $userFilter->setRange(Form::class, $form->id);
        $userFilter->store();

        $affectedUserIds = $userFilter->getUsers();

        // We create RelatedUser entries for all affected users.
        foreach ($affectedUserIds as $userId) {
            $relatedUser = new \StudipCheckin\Models\RelatedUser();
            $relatedUser->form_id = $form->id;
            $relatedUser->user_id = $userId;
            $relatedUser->store();
        }

        return $form;
    }
}
