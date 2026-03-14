import { getCurrentInstance, ref, computed } from 'vue';

export default function useInputElements() {
    const { proxy } = getCurrentInstance();

    const elements = ref([
        { useCase: 'text', type: 'text', displayName: proxy.$gettext('Textfeld'), icon: 'block-typewriter' },
        { useCase: 'textarea', type: 'textarea', displayName: proxy.$gettext('Textfeld'), icon: 'log' },
        { useCase: 'select', type: 'radio', displayName: proxy.$gettext('Optionsfeld'), icon: 'radiobutton-checked' },
        { useCase: 'select', type: 'checkbox', displayName: proxy.$gettext('TrueFalse'), icon: 'accept' },
        { useCase: 'select', type: 'select', displayName: proxy.$gettext('Auswahlfeld'), icon: 'view-list' },
        { useCase: 'pattern', type: 'url', displayName: proxy.$gettext('URL Feld'), icon: 'group' },
        { useCase: 'pattern', type: 'email', displayName: proxy.$gettext('E-Mail Feld'), icon: 'mail' }
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