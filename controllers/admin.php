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
        $helpbar = Helpbar::get();
        $helpbar->addPlainText('', 
            _('Das StudipCheckin Plugin') //TODO add more text
        );
        $helpbar->addPlainText('',  _('Dieses Plugin wurde vom elan e.V. entwickelt. Es steht unter der GNU Affero General Public License, Version 3. Der vollständige Quellcode ist öffentlich zugänglich im GitHub-Repository.'));
        $helpbar->addLink('GNU Affero General Public License', 'https://www.gnu.org/licenses/agpl-3.0.de.html', Icon::create('link-extern', Icon::ROLE_INFO_ALT), '_blank');
        $helpbar->addLink('GitHub-Repository', 'https://github.com/elan-ev/studip-checkin', Icon::create('link-extern', Icon::ROLE_INFO_ALT), '_blank');
        $helpbar->addLink('elan e.V.', 'https://elan-ev.de', Icon::create('link-extern', Icon::ROLE_INFO_ALT), '_blank');
    }
}