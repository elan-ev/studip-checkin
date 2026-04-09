<?php
/**
 * Form Create Handler
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Routes\Form;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;
use StudipCheckin\Models\Form;

use UserFilter;

class Copy extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateForm($user)) {
            throw new AuthorizationFailedException();
        }

        $form = $this->duplicateForm($json);

        return $this->getCreatedResponse($form);

    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (!self::arrayHas($json, 'data.attributes')) {
            return 'Missing `attributes` member of data block.';
        }
        if (!self::arrayHas($json, 'data.attributes.source-id')) {
            return 'Missing `source-id` member of attributes block.';
        }
    }

    private function duplicateForm($json): ?Form
    {
        $sourceForm = Form::find(self::arrayGet($json, 'data.attributes.source-id', null));
        if (!$sourceForm) {
            return null;
        }

        $userFilter = new UserFilter();
        $userFilter->fields = [];
        $userFilter->store();

        $form = Form::create([
            'name' => $sourceForm->name . ' (Kopie vom ' . date('d.m.Y H:i') . ')',
            'description' => $sourceForm->description,
            'filter_id' => $userFilter->getId(),
            'structure' => $sourceForm->structure,
            'version' => 1,
            'start_date' => 0,
            'end_date' => 0
        ]);

        $userFilter->setRange(Form::class, $form->id);
        $userFilter->store();

        return $form;
    }

}