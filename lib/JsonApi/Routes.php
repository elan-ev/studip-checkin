<?php
/**
 * Routes Trait
 *
 * Registriert alle JSON-API-Routen für StudipCheckin.
 * Unterteilt in authentifizierte und nicht authentifizierte Routen.
 *
 * @package   StudipCheckin\JsonApi
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi;

use Slim\Routing\RouteCollectorProxy;

trait Routes
{
    public function registerAuthenticatedRoutes(RouteCollectorProxy $group)
    {
        // Forms.
        $group->get('/checkin-forms', \StudipCheckin\JsonApi\Routes\Form\Index::class);
        $group->get('/checkin-forms/{id}', \StudipCheckin\JsonApi\Routes\Form\Show::class);
        $group->post('/checkin-forms', \StudipCheckin\JsonApi\Routes\Form\Create::class);
        $group->patch('/checkin-forms/{id}', \StudipCheckin\JsonApi\Routes\Form\Update::class);
        $group->delete('/checkin-forms/{id}', \StudipCheckin\JsonApi\Routes\Form\Delete::class);
        // Extra related routes for Forms.
        $group->get('/checkin-forms/{id}/related-users', \StudipCheckin\JsonApi\Routes\Form\RelatedUsersIndex::class);
        $group->get('/checkin-forms/{id}/form-user-data', \StudipCheckin\JsonApi\Routes\Form\FormUserDataIndex::class);

        // Get related users active forms.
        $group->get('/checkin-user-forms/{id}/{mode:all|pending}', \StudipCheckin\JsonApi\Routes\Form\UserFormsIndex::class);

        // Form User Data.
        $group->get('/checkin-form-user-data', \StudipCheckin\JsonApi\Routes\FormUserData\Index::class);
        $group->get('/checkin-form-user-data/{id}', \StudipCheckin\JsonApi\Routes\FormUserData\Show::class);
        $group->post('/checkin-form-user-data', \StudipCheckin\JsonApi\Routes\FormUserData\Create::class);
        $group->patch('/checkin-form-user-data/{id}', \StudipCheckin\JsonApi\Routes\FormUserData\Update::class);
        $group->delete('/checkin-form-user-data/{id}', \StudipCheckin\JsonApi\Routes\FormUserData\Delete::class);

        $group->get('/users/{id}/checkin-form-user-data', \StudipCheckin\JsonApi\Routes\FormUserData\IndexByUser::class);

        // Related Users.
        $group->get('/checkin-related-users', \StudipCheckin\JsonApi\Routes\RelatedUser\Index::class);
        $group->get('/checkin-related-users/{id}', \StudipCheckin\JsonApi\Routes\RelatedUser\Show::class);
        $group->post('/checkin-related-users', \StudipCheckin\JsonApi\Routes\RelatedUser\Create::class);
        $group->patch('/checkin-related-users/{id}', \StudipCheckin\JsonApi\Routes\RelatedUser\Update::class);
        $group->delete('/checkin-related-users/{id}', \StudipCheckin\JsonApi\Routes\RelatedUser\Delete::class);

        // User Filter Fields.
        $group->get('/checkin-user-filter-fields', \StudipCheckin\JsonApi\Routes\UserFilter\UserFilterFieldsIndex::class);
        $group->post('/checkin-user-filters', \StudipCheckin\JsonApi\Routes\UserFilter\UserFiltersCreate::class);
    }
    public function registerUnauthenticatedRoutes(RouteCollectorProxy $group)
    {
    }
}
