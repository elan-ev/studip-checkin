<?php
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/Classes', 'StudipCheckin\\Classes');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/Helpers', 'StudipCheckin\\Helpers');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/JsonApi', 'StudipCheckin\\JsonApi');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/Models', 'StudipCheckin\\Models');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/Events', 'StudipCheckin\\Events');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib/UserFilterFields', 'UserFilterFields\\StudipCheckin');
StudipAutoloader::addAutoloadPath(__DIR__ . '/lib', 'StudipCheckin');

// Observers.
NotificationCenter::addObserver('StudipCheckin\Events\Observers', 'subscribeToUserLogin', 'UserDidLogin');
NotificationCenter::addObserver('StudipCheckin\Events\Observers', 'subscribeToUserCreated', 'UserDidCreate');
