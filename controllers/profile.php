<?php
/**
 * AdminController
 *
 * Controller für die Profilansicht von StudipCheckin.
 *
 * @package   StudipCheckin
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */
class ProfileController extends PluginController
{
    public function index_action()
    {
        PageLayout::disableSidebar();
        if (Navigation::hasItem('/profile')) {
            Navigation::activateItem('/profile/checkin');
        }
    }
}