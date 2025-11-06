<?php
/**
 * Show handler for Form User Data related to a Form.
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
use StudipCheckin\Models\Form;
use StudipCheckin\Models\FormUserData;

class FormUserDataIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        if (!Authority::canIndexForm($user)) {
            throw new AuthorizationFailedException();
        }

        if (!($form = Form::find($args['id']))) {
            throw new RecordNotFoundException();
        }

        list($offset, $limit) = $this->getOffsetAndLimit();
        $resources = FormUserData::findBySQL('`form_id` = ? LIMIT ? OFFSET ?', [$form->id, $limit, $offset]);

        return $this->getPaginatedContentResponse($resources, count($resources));
    }
}
