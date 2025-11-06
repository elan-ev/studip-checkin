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
use StudipCheckin\Models\Form;

use UserFilter;

class Create extends JsonApiController
{
    use ValidationTrait;
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateForm($user)) {
            throw new AuthorizationFailedException();
        }

        // Check for conflicts (e.g., existing form with same filter and name)
        if (!empty(Form::findBySQL('`filter_id` = ?', [
            self::arrayGet($json, 'data.attributes.filter-id', ''),
        ]))) {
            throw new ConflictException('A form with the same filter already exists.');
        }

        $resource = $this->createCheckinForm($json);

        return $this->getCreatedResponse($resource);
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
    }

    private function createCheckinForm($json)
    {
        $filterId = self::arrayGet($json, 'data.attributes.filter-id', '');
        $name = self::arrayGet($json, 'data.attributes.name', '');
        $structure = self::arrayGet($json, 'data.attributes.structure', '');

        $form = new Form();
        $form->filter_id = $filterId;
        $form->name = $name;
        $form->structure = $structure;
        $form->version = 1;
        $form->store();

        // Here we then get the filter and compile the list of affected users.
        $userFilter = new UserFilter($filterId);
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
