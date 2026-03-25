<?php
/**
 * Related User Create Handler
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Routes\RelatedUser;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\ConflictException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\RelatedUser;
use StudipCheckin\Models\Form;

class Create extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateRelatedUser($user)) {
            throw new AuthorizationFailedException();
        }

        // Check for conflicts (e.g., existing related user)
        if (!empty(RelatedUser::findBySQL('`form_id` = ? AND `user_id = ?`', [
            self::arrayGet($json, 'data.attributes.form-id', ''),
            self::arrayGet($json, 'data.attributes.user-id', ''),
        ]))) {
            throw new ConflictException('A related user with the data already exists.');
        }

        $relatedUser = $this->createCheckinRelatedUser($json);
        return $this->getCreatedResponse($relatedUser);
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
        if (!self::arrayHas($json, 'data.attributes.form-id')) {
            return 'Missing `form-id` member of attributes block.';
        }
        if (!self::arrayHas($json, 'data.attributes.user-id')) {
            return 'Missing `user-id` member of attributes block.';
        }
        // Check if user exists.
        if (!($user = \User::find(self::arrayGet($json, 'data.attributes.user-id', '')))) {
            return 'User does not exists!';
        }
        // Check if form exists.
        if (!($form = Form::find(self::arrayGet($json, 'data.attributes.form-id', '')))) {
            return 'Form does not exists!';
        }
    }

    protected function createCheckinRelatedUser($json)
    {
        $formId = self::arrayGet($json, 'data.attributes.form-id', '');
        $userId = self::arrayGet($json, 'data.attributes.user-id', '');

        $relatedUser = new RelatedUser();
        $relatedUser->form_id = $formId;
        $relatedUser->user_id = $userId;

        $relatedUser->store();

        return $relatedUser;
    }
}
