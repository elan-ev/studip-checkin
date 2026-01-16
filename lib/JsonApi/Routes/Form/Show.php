<?php
/**
 * Form Show Handler
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
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\JsonApi\Schemas\FormSchema;
use StudipCheckin\Models\Form;

class Show extends JsonApiController
{
    protected $allowedIncludePaths = [
        FormSchema::REL_FORM_USER_DATA,
        FormSchema::REL_RELATED_USERS,
        FormSchema::REL_USER_FILTER,
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        if (!Authority::canShowForm($user)) {
            throw new AuthorizationFailedException();
        }

        $form = Form::find($args['id']);
        if (!$form) {
            throw new RecordNotFoundException();
        }

        return $this->getContentResponse($form);
    }
}
