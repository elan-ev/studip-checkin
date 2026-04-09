<?php
/**
 * Helper class for StudipCheckin.
 *
 * @package   StudipCheckin\Helper
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\Helper;

use StudipCheckin\Models\RelatedUser;
use StudipCheckin\Models\Form;
use StudipCheckin\Models\FormUserData;

class CheckinBrain {

    /**
     * Retrieves all pending forms for a user that require submission.
     *
     * A form is considered "pending" if all of the following conditions are met:
     * - The user is an active related user for the form
     * - The form is within its runnable date range (if a date range is defined)
     * - The user has not yet submitted data for the current version of the form
     *
     * @param string $userId The unique identifier of the user
     * @return array Array of Form objects that are pending user submission.
     *               Returns an empty array if no pending forms are found.
     */
    public static function getUserPendingForm(string $userId): array
    {
        $pendingForms = [];
        $isActiveRelatedUser = RelatedUser::hasActiveRecord($userId);
        if ($isActiveRelatedUser) {
            $forms = RelatedUser::getActiveFormsByUser($userId);
            foreach ($forms as $form) {
                // If the form has date range and it is not expired?
                if (!$form->isRunnable()) {
                    continue;
                }
                // If the user has already answer this version of the form!?
                $formData = FormUserData::findByUserFormVersion($form->id, $userId, $form->version);
                if (!empty($formData)) {
                    continue;
                }
                $pendingForms[] = $form;
            }
        }

        return $pendingForms;
    }

    /**
     * Goes through all forms and checks if the user fits in any filter criteria and user is not recorded yet.
     * If so, creates a RelatedUser record for the user and form.
     * @param string $userId User ID
     * @return void
     */
    public static function checkAndRecordRelatedUser(string $userId): void
    {
        // Check if user fits in any filter criteria and then save it!
        $forms = Form::getAll();
        foreach ($forms as $form) {
            $userFilter = new \UserFilter($form->filter_id);
            if (
                $userFilter->isFulfilled($userId) &&
                !RelatedUser::hasAnyRecordByForm($userId, $form->id)
            ) {
                // User fits the filter, create RelatedUser entry.
                $relatedUser = new RelatedUser();
                $relatedUser->form_id = $form->id;
                $relatedUser->user_id = $userId;
                $relatedUser->store();
            }
        }
    }
}
