<?php
/**
 * Form User Data Index Handler
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
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

class IndexByUser extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = \User::find($args['id']);
        if (!$user) {
            throw new RecordNotFoundException();
        }

        $requestingUser = $this->getUser($request);
        if (!Authority::canIndexUsersFormUserData($user, $requestingUser)) {
            throw new AuthorizationFailedException();
        }

        list($offset, $limit) = $this->getOffsetAndLimit();
        $formUserData = FormUserData::findBySQL('`user_id` = ? LIMIT ? OFFSET ?', [$user->id, $limit, $offset]);

        return $this->getPaginatedContentResponse($formUserData, count($formUserData));
    }

}