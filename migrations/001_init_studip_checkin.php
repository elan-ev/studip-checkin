<?php
/**
 * InitStudipCheckin
 *
 * Migration zum Initialisieren der Datenbanktabellen für das StudipCheckin-Plugin.
 *
 * @package   StudipCheckin
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 */
final class InitStudipCheckin extends Migration
{
    public function description()
    {
        return 'Init database tables for StudipCheckin plugin.';
    }

    public function up()
    {
        $db = DBManager::get();

        $db->exec("CREATE TABLE IF NOT EXISTS `checkin_related_users` (
            `id`                            INT(11) NOT NULL AUTO_INCREMENT,
            `form_id`                       INT(11) NOT NULL,
            `user_id`                       CHAR(32) NOT NULL,
            `active`                        TINYINT(1) NOT NULL DEFAULT '1',
            `hide`                          TINYINT(1) NOT NULL DEFAULT '0',
            `mkdate`                        INT(11) UNSIGNED NOT NULL,
            `chdate`                        INT(11) UNSIGNED NOT NULL,

            PRIMARY KEY (`id`),
            UNIQUE KEY `user_form_unique` (`user_id`, `form_id`),
            INDEX `status_check_idx` (`user_id`, `active`),
            INDEX `form_id_idx` (`form_id`)
            )"
        );

        $db->exec("CREATE TABLE IF NOT EXISTS `checkin_forms` (
            `id`                            INT(11) NOT NULL AUTO_INCREMENT,
            `filter_id`                     char(32)  NOT NULL,
            `name`                          MEDIUMTEXT NOT NULL,
            `description`                   MEDIUMTEXT DEFAULT NULL,
            `structure`                     MEDIUMTEXT NOT NULL,
            `start_date`                    INT(11) UNSIGNED NOT NULL,
            `end_date`                      INT(11) UNSIGNED NOT NULL,
            `version`                       INT(11) NOT NULL,
            `mkdate`                        INT(11) UNSIGNED NOT NULL,
            `chdate`                        INT(11) UNSIGNED NOT NULL,

            PRIMARY KEY (`id`),
            UNIQUE KEY `filter_id_unique` (`filter_id`)
            )"
        );

        $db->exec("CREATE TABLE IF NOT EXISTS `checkin_form_user_data` (
            `id`                            INT(11) NOT NULL AUTO_INCREMENT,
            `form_id`                       INT(11) NOT NULL,
            `user_id`                       CHAR(32) NOT NULL,
            `form_data`                     MEDIUMTEXT NOT NULL,
            `form_version`                  INT(11) NOT NULL,
            `mkdate`                        INT(11) UNSIGNED NOT NULL,
            `chdate`                        INT(11) UNSIGNED NOT NULL,

            PRIMARY KEY (`id`),
            UNIQUE KEY `user_form_unique` (`user_id`, `form_id`),
            INDEX `form_id_idx` (`form_id`),
            INDEX `user_id_idx` (`user_id`)
            )"
        );
    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec("DROP TABLE IF EXISTS `checkin_related_users`");
        $db->exec("DROP TABLE IF EXISTS `checkin_forms`");
        $db->exec("DROP TABLE IF EXISTS `checkin_form_user_data`");
    }
}
