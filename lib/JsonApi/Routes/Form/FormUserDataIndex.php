<?php
/**
 * Show handler for Form User Data related to a Form.
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
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
        $total = count(FormUserData::findBySQL('`form_id` = ? ', [$form->id]));
        $formUserData = FormUserData::findBySQL(
            'JOIN auth_user_md5 ON (checkin_form_user_data.user_id = auth_user_md5.user_id) WHERE checkin_form_user_data.form_id = ? ORDER BY auth_user_md5.username ASC LIMIT ? OFFSET ?',
            [$form->id, $limit, $offset]
        );

        $apiResponse = $this->getPaginatedContentResponse($formUserData, $total);

        $payload = json_decode((string) $apiResponse->getBody(), true);
        $payload['meta']['page']['hasMore'] = ($offset + count($formUserData)) < $total;
        $apiResponse->getBody()->rewind();
        $apiResponse->getBody()->write(json_encode($payload));

        return $apiResponse;
    }
}
