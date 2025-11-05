<?php
class AdminController extends PluginController
{
    public function index_action()
    {
        PageLayout::disableSidebar();
    }
}