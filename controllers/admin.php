<?php
/**
 * AdminController
 *
 * Controller für die Adminansicht von StudipCheckin.
 *
 * @package   StudipCheckin
 * @since     0.1.0
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @copyright 2025 elan e.V.
 * @license   GPL-3.0 WITH License-Supplement (see LICENSE-SUPPLEMENT.txt)
 * @link      https://elan-ev.de
 */
class AdminController extends PluginController
{
    public function index_action()
    {
        StudipCheckin\Helper\AttributionHelper::addLicenseToHelpbar();
        PageLayout::disableSidebar();
        if (Navigation::hasItem('/contents')) {
            Navigation::activateItem('/contents/checkin');
        }
    }

    public function export_action()
    {
        $request = iterator_to_array(Request::getInstance());
        $form_id = $request['id'];
        $filename = 'export';
        $headers = [];
        $csvdata = [];

        if ($form_id) {
            $form = StudipCheckin\Models\Form::find($form_id);
            $filename = str_replace(' ', '_', $form->name);
            $structure = json_decode($form->structure, true);
            $staticHeaders = ['Nachname', 'Vorname', 'Username', 'Matrikelnummer'];
            $form_headers = array_column(array_column($structure, 'payload'), 'label');
            $headers = array_merge($staticHeaders, $form_headers);

            $formUserData = StudipCheckin\Models\FormUserData::findBySQL('`form_id` = ?', [$form->id]);

            foreach ($formUserData as $data) {
                $user = User::find($data->user_id);
                $user_information = [$user->nachname, $user->vorname, $user->username, $user->matriculation_number];
                $form_data = json_decode($data->form_data, true);
                $user_data = [];
                foreach ($structure as $field) {
                    $id = $field['id'];
                    $rawValue = $form_data[$id] ?? null;
                    $user_data[] = $this->transformValue($field, $rawValue);
                }

                $csvdata[] = array_merge($user_information, $user_data);
            }
        }

        $this->render_csv(array_merge([$headers], $csvdata), "{$filename}.csv");
    }

    private function transformValue($field, $rawValue)
    {
        if ($rawValue === null)
            return '';

        $type = $field['type'] ?? '';
        $options = $field['payload']['options'] ?? [];

        return match ($type) {
            'radio' => $options[$rawValue]['text'] ?? $rawValue,
            'multiselect' => is_array($rawValue)
            ? implode(', ', array_map(fn($idx) => $options[$idx]['text'] ?? $idx, $rawValue))
            : $rawValue,
            'switch' => $rawValue ? 'Ja' : 'Nein',
            default => is_array($rawValue) ? implode(', ', $rawValue) : $rawValue,
        };
    }
}