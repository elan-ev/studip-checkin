<?php

/**
 * IndexController
 *
 * Controller für die Indexansicht von StudipCheckin.
 *
 * @package   StudipCheckin
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */
class IndexController extends PluginController
{
    public function index_action($userId)
    {
        PageLayout::disableSidebar();
    }
}