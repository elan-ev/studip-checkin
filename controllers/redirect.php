<?php

/**
 * RedirectController
 *
 * Controller für die Redirectansicht von StudipCheckin.
 *
 * @package   StudipCheckin
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */
class RedirectController extends PluginController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $this->user_id = $GLOBALS['user']->id;
        PageLayout::setHelpKeyword('CheckIn');
    }

    public function index_action($userId)
    {
        PageLayout::setTitle(_('Awesome CheckIn!'));
        PageLayout::setTabNavigation(NULL);
        PageLayout::disableSidebar();

        $this->userId = $userId;
    }
}
