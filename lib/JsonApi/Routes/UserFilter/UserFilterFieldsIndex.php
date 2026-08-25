<?php
/**
 * StudipCheckin UserFilterFields Index Handler
 *
 * @package   StudipCheckin\JsonApi\Routes
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
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

        $dir = __DIR__ . '/../../../UserFilterFields';

        // foreach (glob($dir . '/*.php') as $file) {
        //     require_once $file;
        // }

        require_once $dir . '/StudipCheckinDegreeFilter.php';
        require_once $dir . '/StudipCheckinDomainFilter.php';
        require_once $dir . '/StudipCheckinGenderFilter.php';
        require_once $dir . '/StudipCheckinInstituteFilter.php';
        require_once $dir . '/StudipCheckinPermissionFilter.php';
        // we do not need Statusgroups right now!

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
