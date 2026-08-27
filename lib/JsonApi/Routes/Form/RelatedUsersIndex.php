<?php
/**
 * Show handler for Related Users related to a Form
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
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\Form;
use StudipCheckin\Models\RelatedUser;

class RelatedUsersIndex extends JsonApiController
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
        $total = count(RelatedUser::findBySQL('`form_id` = ?', [$form->id]));
        $relatedUsers = RelatedUser::findBySQL(
            'JOIN auth_user_md5 ON (checkin_related_users.user_id = auth_user_md5.user_id) WHERE checkin_related_users.form_id = ? ORDER BY auth_user_md5.username ASC LIMIT ? OFFSET ?',
            [$form->id, $limit, $offset]
        );

        $apiResponse = $this->getPaginatedContentResponse($relatedUsers, $total);

        $payload = json_decode((string) $apiResponse->getBody(), true);
        $payload['meta']['page']['hasMore'] = ($offset + count($relatedUsers)) < $total;
        $apiResponse->getBody()->rewind();
        $apiResponse->getBody()->write(json_encode($payload));

        return $apiResponse;
    }
}
