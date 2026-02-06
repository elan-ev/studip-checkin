<?php
/**
 * User Forms Index Handler
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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Helper\CheckinBrain;

class UserFormsIndex extends JsonApiController
{
    protected $allowedIncludePaths = [];
    public function __invoke(Request $request, Response $response, $args)
    {
        $requestedUser = $this->getUser($request);
        $user = \User::find($args['id']);
        if (!Authority::canIndexUserForms($user, $requestedUser)) {
            throw new AuthorizationFailedException();
        }

        $pendingForms = CheckinBrain::getUserPendingForm($user->id);

        return $this->getContentResponse($pendingForms);
    }
}
