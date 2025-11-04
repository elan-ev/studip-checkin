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
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\JsonApi;

trait Routes
{
    public function registerAuthenticatedRoutes(\Slim\Routing\RouteCollectorProxy $group)
    {
    }
    public function registerUnauthenticatedRoutes(\Slim\Routing\RouteCollectorProxy $group)
    {
    }
}