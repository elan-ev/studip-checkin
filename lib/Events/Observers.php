<?php
/**
 * Observers class for StudipCheckin.
 *
 * @package   StudipCheckin\Events
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\Events;

use StudipCheckin\Helper\CheckinBrain;

class Observers {
    /**
     * Subscribe to user login event.
     *
     * @Notification UserDidLogin
     *
     * @param string $event The event type.
     * @param string $userId The ID of the logged-in user.
     */
    public static function subscribeToUserLogin($event, $userId) {
        // Check if user has pending form.
        if (!empty(CheckinBrain::getUserPendingForm($userId))) {
            // Prepare redirect URL to checkin page, in order to show forms.
            $url = \PluginEngine::getURL('StudipCheckin', [], 'redirect/index/' . $userId);

            // The only way to redirect after login in Stud.IP is to set this session variable.
            $_SESSION['redirect_after_login'] = $url;
        }
    }

    /**
     * Subscribe to user login event.
     *
     * @Notification UserDidLogin
     *
     * @param string $event The event type.
     * @param SimpleORMap $user the created user SimpleORMap object.
     */
    public static function subscribeToUserCreated($event, $user) {
        CheckinBrain::checkAndRecordRelatedUser($user->id);
    }
}
