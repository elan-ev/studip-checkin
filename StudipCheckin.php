<?php

/**
 * StudipCheckin Plugin
 *
 * @package   StudipCheckin
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

require_once __DIR__ . '/bootstrap.php';

use JsonApi\Contracts\JsonApiPlugin;
use StudipCheckin\JsonApi\Routes;
use StudipCheckin\JsonApi\Schemas;

class StudipCheckin extends StudIPPlugin implements SystemPlugin, JsonApiPlugin
{
    use Routes;
    use Schemas;
    public function __construct()
    {
        parent::__construct();

        PageLayout::addScript($this->getPluginUrl() . '/dist/studip-checkin.js', [
            'type' => 'module',
            'rel' => 'preload',
        ]);
        PageLayout::addScript($this->getPluginUrl() . '/dist/studip-checkin-admin.js', [
            'type' => 'module',
            'rel' => 'preload',
        ]);
        PageLayout::addScript($this->getPluginUrl() . '/dist/studip-checkin-profile.js', [
            'type' => 'module',
            'rel' => 'preload',
        ]);
        PageLayout::addStylesheet($this->getPluginUrl() . '/dist/drawer.css');
        PageLayout::addStylesheet($this->getPluginUrl() . '/dist/StudipActionMenu.css');
        PageLayout::addStylesheet($this->getPluginUrl() . '/dist/studip-checkin-admin.css');
        PageLayout::addStylesheet($this->getPluginUrl() . '/dist/studip-checkin-profile.css');

        if ($GLOBALS['perm']->have_perm("admin")) {
            $this->addContentsNavigation();
        }

        $this->addProfileNavigation();

    }

    public function perform($unconsumedPath)
    {
        parent::perform($unconsumedPath);
    }

    public function getPluginName(): string
    {
        return _('Check-In Plugin');
    }

    public function getInfoTemplate($courseId)
    {
        return null;
    }

    private function addContentsNavigation(): void
    {
        if (Navigation::hasItem('/contents')) {
            Navigation::addItem('/contents/checkin', $this->buildContentsNavigation());
        }

    }

    private function addProfileNavigation(): void
    {
        if (Navigation::hasItem('/profile')) {
            Navigation::addItem('/profile/checkin', $this->buildProfileNavigation());
        }
    }

    private function buildContentsNavigation(): Navigation
    {
        $navigation = new Navigation($this->_('CheckInPlugin'));
        $navigation->setDescription('TODO: Lorem ipsum dolor');
        $navigation->setImage(Icon::create('place', 'navigation'));
        $navigation->addSubNavigation('index', new Navigation(
            'Übersicht',
            PluginEngine::getURL($this, [], 'admin')
        ));

        return $navigation;

    }

    private function buildProfileNavigation(): Navigation
    {
        $navigation = new Navigation($this->_('CheckInPlugin'));
        $navigation->setDescription('TODO: Lorem ipsum dolor');
        $navigation->setImage(Icon::create('place', 'navigation'));
        $navigation->addSubNavigation('index', new Navigation(
            'CheckIn',
            PluginEngine::getURL($this, [], 'profile')
        ));

        return $navigation;

    }

}
