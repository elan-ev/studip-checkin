<?php
/**
 * Helper class for StudipCheckin.
 *
 * @package   StudipCheckin\Helper
 * @since     0.1.0
 * @author    Farbod Zamani <zamani@elan-ev.de>
 * @copyright 2026 elan e.V.
 * @license   AGPL-3.0
 * @link      https://elan-ev.de
 */

namespace StudipCheckin\Helper;

use StudipCheckin\Models\RelatedUser;
use StudipCheckin\Models\Form;
use StudipCheckin\Models\FormUserData;

class CheckinBrain {

    /**
     * Ensure user has pending form to fill.
     * First checks if user is an active related user,
     * then checks if there is any form without submitted data for the current version.
     *
     * @param string $userId User Id
     * @return bool
     */
    public static function ensureUserHasPendingForm(string $userId): bool
    {
        $isActiveRelatedUser = RelatedUser::hasActiveRecord($userId);
        if ($isActiveRelatedUser) {
            $forms = RelatedUser::getActiveFormsByUser($userId);
            foreach ($forms as $form) {
                $formData = FormUserData::findByUserFormVersion($form->id, $userId, $form->version);
                if (!$formData) {
                    return true;
                }
            }
        }

        return false;
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

    /**
     * Consider all aspects and validate if FormUserData can be created for given user and form.
     * @param string $userId User ID
     * @param int $formId Form ID
     * @return bool
     */
    public static function validateFormUserDataRelatedRecords(string $userId, int $formId): bool
    {
        $form = Form::find($formId);
        if (!$form) {
            return false;
        }

        $activeRelatedUser = RelatedUser::findActiveRecordByUserAndForm($userId, $formId);
        if (!$activeRelatedUser) {
            return false;
        }

        // TODO: This can be optimized to throw Conflict Error if exists, but currently considered as not found!
        $formData = FormUserData::findByUserFormVersion($formId, $userId, $form->version);
        if ($formData) {
            return false;
        }

        return true;
    }
}
