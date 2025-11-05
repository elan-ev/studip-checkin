<?php
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