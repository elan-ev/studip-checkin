import { ref, computed } from 'vue';
import { useGettext } from 'vue3-gettext';

export default function useInputElements() {
    const { $gettext } = useGettext();

    const elements = ref([
        { useCase: 'text', type: 'text', displayName: $gettext('Eingabezeile'), icon: 'block-typewriter' },
        { useCase: 'textarea', type: 'textarea', displayName: $gettext('Mehrzeiliges Textfeld'), icon: 'log' },
        { useCase: 'select', type: 'radio', displayName: $gettext('Einfachauswahl'), icon: 'radiobutton-checked' },
        { useCase: 'select', type: 'switch', displayName: $gettext('Ja/Nein-Schalter'), icon: 'accept' },
        { useCase: 'select', type: 'multiselect', displayName: $gettext('Mehrfachauswahl'), icon: 'checkbox-checked'}, 
        { useCase: 'select', type: 'select', displayName: $gettext('Auswahlliste'), icon: 'view-list' },
        { useCase: 'pattern', type: 'url', displayName: $gettext('URL'), icon: 'group' },
        { useCase: 'pattern', type: 'email', displayName: $gettext('E-Mail'), icon: 'mail' },
        { useCase: 'pattern', type: 'number', displayName: $gettext('Zahlenfeld'), icon: 'arr_1sort' }
    ]);

    const getElementsByUseCase = (useCase) => {
        return elements.value.filter(el => el.useCase === useCase);
    };

    const addCustomElement = (newElement) => {
        elements.value.push(newElement);
    };

    return {
        inputElements: computed(() => elements.value),
        getElementsByUseCase,
        addCustomElement
    };
}