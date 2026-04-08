<?php
/**
 * AddRoles
 *
 * Migration zum Hinzufügen von Rollen für das StudipCheckin-Plugin.
 *
 * @package   StudipCheckin
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 */
final class AddRoles extends Migration
{
    const rolename = 'CheckinPlugin_Admin';

    public function description()
    {
        return 'add admin roles for the CheckinPlugin';
    }

    public function up()
    {
        $role = new Role();

        $role->setRolename(self::rolename);
        $role->setSystemtype(false);

        RolePersistence::saveRole($role);
    }

    public function down()
    {
        $db = DBManager::get();

        $stmt = $db->prepare('SELECT * FROM roles WHERE rolename = ?');
        $stmt->execute([self::rolename]);

        while ($data = $stmt->fetch()) {
            $role = new Role($data['roleid'], $data['rolename']);

            RolePersistence::deleteRole($role);
        }
    }
}