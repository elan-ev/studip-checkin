<?php
/**
 * Related User Update Handler
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
use JsonApi\Routes\ValidationTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\RelatedUser;

class Update extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canUpdateRelatedUser($user)) {
            throw new AuthorizationFailedException();
        }

        $relatedUser = RelatedUser::find($args['id']);
        if (!$relatedUser) {
            throw new RecordNotFoundException();
        }

        $updatedRelatedUser = $this->updateCheckinRelatedUser($relatedUser, $json);
        $updatedRelatedUser->id = '';
        return $this->getContentResponse($updatedRelatedUser);
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
        if (!self::arrayHas($json, 'data.attributes.active')) {
            return 'Missing `active` member of attributes block.';
        }
        if (!self::arrayHas($json, 'data.attributes.hide')) {
            return 'Missing `hide` member of attributes block.';
        }
    }

    protected function updateCheckinRelatedUser($relatedUser, $json)
    {
        $active = self::arrayGet($json, 'data.attributes.active', '');
        $hide = self::arrayGet($json, 'data.attributes.hide', '');

        $relatedUser->active = (int) $active;
        $relatedUser->hide = (int) $hide;

        $relatedUser->store();

        return $relatedUser;
    }
}
