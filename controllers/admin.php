<?php
/**
 * AdminController
 *
 * Controller für die Adminansicht von StudipCheckin.
 *
 * @package   StudipCheckin
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */
class AdminController extends PluginController
{
    public function index_action()
    {
        PageLayout::disableSidebar();
        if (Navigation::hasItem('/contents')) {
            Navigation::activateItem('/contents/checkin');
        }
    }
}