<?php
/**
 * Form Delete Handler
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
use StudipCheckin\Models\Form;

class Delete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        if (!Authority::canDeleteForm($user)) {
            throw new AuthorizationFailedException();
        }

        $form = Form::find($args['id']);
        if (!$form) {
            throw new RecordNotFoundException();
        }

        // Here when deleting the form, we need to make sure no orphan entry stays behind.
        $linkedUserFilter = new \UserFilter($form->filter_id);
        if ($linkedUserFilter->getId() === $form->filter_id && $linkedUserFilter->range_type === Form::class &&
            $linkedUserFilter->range_id === $form->id) {
            $linkedUserFilter->delete();
        }

        // As the SimpleORMap relationship applies,
        // by deleting form all related users and form user data entries will be deleted as well.
        $form->delete();

        return $this->getCodeResponse(204);
    }
}
