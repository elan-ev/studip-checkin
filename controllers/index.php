<?php
class IndexController extends PluginController
{
    public function index_action($userId)
    {
        PageLayout::disableSidebar();
    }
}