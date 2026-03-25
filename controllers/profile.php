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
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */
class ProfileController extends PluginController
{
    public function index_action()
    {
        StudipCheckin\Helper\AttributionHelper::addLicenseToHelpbar();

        PageLayout::disableSidebar();

        if (Navigation::hasItem('/profile')) {
            Navigation::activateItem('/profile/checkin');
        }
    }
}