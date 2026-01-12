<?php
/**
 * User Filter create Handler
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi\Routes\UserFilter;

use JsonApi\Errors\AuthorizationFailedException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

use StudipCheckin\JsonApi\Routes\Authority;

/**
 * Create a new UserFilter for StudipCheckin.
 */
class UserFiltersCreate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        // TODO: decide whether to use this Handler of use the core route `user-filters` for creation.
        $filter = new \UserFilter();
        $filter->show_user_count = true;

        $json = $this->validate($request);
        $user = $this->getUser($request);

        if (!Authority::canCreateUserFilters($user, $filter)) {
            throw new AuthorizationFailedException();
        }

        foreach (self::arrayGet($json, 'data.attributes.filters') as $one) {
            $classname = '\\' . $one['attributes']['type'];
            $field = !empty($one['attributes']['typeparam'])
                ? new $classname($one['attributes']['typeparam'])
                : new $classname();
            $field->setValue($one['attributes']['value']);
            $field->setCompareOperator($one['attributes']['compare-operator']);
            $filter->addField($field);
        }

        $filter->id = '';

        return $this->getCreatedResponse($filter);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (!self::arrayHas($json, 'data.attributes')) {
            return 'Missing `attributes` member of data block.';
        }
        if (!self::arrayHas($json, 'data.attributes.filters')) {
            return 'Missing `filters` member of attributes block.';
        }
    }

}
