import { getCurrentInstance, ref, computed } from 'vue';

export default function useInputElements() {
    const { proxy } = getCurrentInstance();

    const elements = ref([
        { useCase: 'text', type: 'text', displayName: proxy.$gettext('Eingabezeile'), icon: 'block-typewriter' },
        { useCase: 'textarea', type: 'textarea', displayName: proxy.$gettext('Mehrzeiliges Textfeld'), icon: 'log' },
        { useCase: 'select', type: 'radio', displayName: proxy.$gettext('Einfachauswahl'), icon: 'radiobutton-checked' },
        { useCase: 'select', type: 'switch', displayName: proxy.$gettext('Ja/Nein-Schalter'), icon: 'accept' },
        { useCase: 'select', type: 'multiselect', displayName: proxy.$gettext('Mehrfachauswahl'), icon: 'checkbox-checked'}, 
        { useCase: 'select', type: 'select', displayName: proxy.$gettext('Auswahlliste'), icon: 'view-list' },
        { useCase: 'pattern', type: 'url', displayName: proxy.$gettext('URL'), icon: 'group' },
        { useCase: 'pattern', type: 'email', displayName: proxy.$gettext('E-Mail'), icon: 'mail' },
        { useCase: 'pattern', type: 'number', displayName: proxy.$gettext('Zahlenfeld'), icon: 'arr_1sort' }
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