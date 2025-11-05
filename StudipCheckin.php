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

class StudipCheckin extends StudIPPlugin implements StandardPlugin, SystemPlugin, JsonApiPlugin
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

        PageLayout::addStylesheet($this->getPluginUrl() . '/dist/studip-checkin.css');

    }

    // At the moment we don't need the perform.
    
    // public function perform($unconsumedPath)
    // {
    //     // This require must be here, to prevent vendor version conflicts.
    //     require_once __DIR__ . '/vendor/autoload.php';

    //     $trails_root = $this->getPluginPath() . '/app';

    //     $dispatcher = new Trails_Dispatcher(
    //         $trails_root,
    //         rtrim(PluginEngine::getURL($this, [], ''), '/'),
    //         'index'
    //     );

    //     $dispatcher->current_plugin = $this;
    //     $dispatcher->dispatch($unconsumedPath);
    // }

    public function getPluginName(): string
    {
        return _('Check-In Plugin');
    }

    public function getInfoTemplate($courseId)
    {
        return null;
    }

    public function getTabNavigation($courseId)
    {
        return [];
    }

    public function getIconNavigation($courseId, $last_visit, $user_id)
    {
        return null;
    }

}
