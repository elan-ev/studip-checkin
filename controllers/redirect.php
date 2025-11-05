<?php
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
