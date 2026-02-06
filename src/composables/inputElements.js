export default function useInputElements() {
    // TODO all configs, naming, more/better attributes?

    const elements = [
        {
            useCase: 'text',
            type: 'text',
            displayName: 'Textfeld',
            icon: '',
        },
        {
            useCase: 'select',
            type: 'radio',
            displayName: 'Optionsfeld',
            icon: '',
        },
        {
            useCase: 'select',
            type: 'checkbox',
            displayName: 'TrueFalse',
            icon: '',
        },
        {
            useCase: 'select',
            type: 'select',
            displayName: 'Auswahlfeld',
            icon: '',
        },
        {
            useCase: 'pattern',
            type: 'url',
            displayName: 'URL Feld',
            icon: '',
        },
        {
            useCase: 'pattern',
            type: 'email',
            displayName: 'E-Mail Feld',
            icon: '',
        }
    ];

    return elements;
}
