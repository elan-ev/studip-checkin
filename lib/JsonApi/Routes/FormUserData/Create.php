<?php
/**
 * Form User Data Create Handler
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Routes\FormUserData;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\ConflictException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\Form;
use StudipCheckin\Models\FormUserData;
use StudipCheckin\Models\RelatedUser;

class Create extends JsonApiController
{
    use ValidationTrait;

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateFormUserData($user)) {
            throw new AuthorizationFailedException();
        }

        $formVersion = self::arrayGet($json, 'data.attributes.form-version', 0);
        $formData = self::arrayGet($json, 'data.attributes.form-data', []);
        $formId = self::arrayGet($json, 'data.attributes.form-id', 0);

        $form = Form::find($formId);
        $activeRelatedUser = RelatedUser::findActiveRecordByUserAndForm($user->id, $formId);

        if (!$form || !$activeRelatedUser) {
            throw new RecordNotFoundException();
        }

        $formUserData = FormUserData::findByFormUser($formId, $user->id);
        if (
            ((int) $formVersion !== (int) $form->version) ||
            ($formUserData && (int) $formUserData->form_version === (int) $form->version)
        ) {
            throw new ConflictException('There was a conflic either by existing record or mismatching versions');
        }

        if (empty($formUserData)) {
            $formUserData = new FormUserData();
            $formUserData->form_id = (int) $formId;
            $formUserData->user_id = $user->id;
        }

        $formUserData->form_version = (int) $formVersion;
        $formUserData->form_data = $formData;
        $formUserData->store();

        return $this->getCreatedResponse($formUserData);
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
        if (!self::arrayHas($json, 'data.attributes.form-version')) {
            return 'Missing `form-version` member of attributes block.';
        }
        if (!self::arrayHas($json, 'data.attributes.form-data')) {
            return 'Missing `form-data` member of attributes block.';
        }
        // Make sure form-data is valid json or not empty.
        $formData = self::arrayGet($json, 'data.attributes.form-data', []);
        if (empty($formData)) {
            return '`form-data` is required.';
        }
    }
}
