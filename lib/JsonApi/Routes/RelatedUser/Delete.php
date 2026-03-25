<?php
/**
 * Related User Delete Handler
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
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\RelatedUser;
use StudipCheckin\Models\FormUserData;

class Delete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        if (!Authority::canDeleteRelatedUser($user)) {
            throw new AuthorizationFailedException();
        }

        $relatedUser = RelatedUser::find($args['id']);
        if (!$relatedUser) {
            throw new RecordNotFoundException();
        }

        $relatedUser->delete();

        // Here we should also remove the FormUserData related to this user.
        $formUserData = FormUserData::findOneBySQL('`form_id` = ? AND `user_id` = ?',
            [$relatedUser->form_id, $relatedUser->user_id]);

        if ($formUserData) {
            $formUserData->delete();
        }

        return $this->getCodeResponse(204);
    }
}
