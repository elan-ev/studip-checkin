<?php
/**
 * Form User Data Update Handler
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Routes\FormUserData;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\FormUserData;

class Update extends JsonApiController
{
    use ValidationTrait;

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $formUserData = FormUserData::find($args['id']);
        if (!$formUserData) {
            throw new RecordNotFoundException();
        }

        $requestingUser = $this->getUser($request);
        $user = $formUserData->user;
        if (!Authority::canUpdateFormUserData($user, $requestingUser)) {
            throw new AuthorizationFailedException();
        }

        $json = $this->validate($request);

        $formVersion = self::arrayGet($json, 'data.attributes.form-version', 0);
        $formData = self::arrayGet($json, 'data.attributes.form-data', []);

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
