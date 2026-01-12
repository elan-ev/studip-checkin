<?php
/**
 * StudipCheckin UserFilterFields Index Handler
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
use JsonApi\JsonApiController;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use StudipCheckin\JsonApi\Routes\Authority;

class UserFilterFieldsIndex extends JsonApiController
{
    protected $allowedFilteringParameters = ['target', 'context'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        if (!Authority::canIndexUserFilterFields($user)) {
            throw new AuthorizationFailedException();
        }

        // TODO: This part is important for the inclusion of the user filters. We can also improve it later!
        $dir = __DIR__ . '/../../../UserFilterFields';

        foreach (glob($dir . '/*.php') as $file) {
            require_once $file;
        }

        $filtering = $this->getQueryParameters()->getFilteringParameters() ?: [];
        $target = $filtering['target'] ?? '';
        $context = $filtering['context'] ?? 'StudipCheckin';

        $fields = [];
        foreach (\UserFilterField::getAvailableFilterFields($context, $target) as $class => $name) {
            if (str_contains($class, '_')) {
                [$classname, $typeparam] = explode('_', $class);
                $fields[] = new $classname($typeparam);
            } else {
                $fields[] = new $class();
            }
        }

        return $this->getContentResponse($fields);
    }
}
