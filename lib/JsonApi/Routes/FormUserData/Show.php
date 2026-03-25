<?php
/**
 * Form User Data Show Handler
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
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\FormUserData;

class Show extends JsonApiController
{
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
        if (!Authority::canShowFormUserData($user, $requestingUser)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($formUserData);
    }
}
