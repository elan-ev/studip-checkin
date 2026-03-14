export default function useInputElements() {
    // TODO all configs, naming, more/better attributes?

    const elements = [
        {
            useCase: 'text',
            type: 'text',
            displayName: 'Textfeld',
            icon: 'block-typewriter',
        },
        {
            useCase: 'select',
            type: 'radio',
            displayName: 'Optionsfeld',
            icon: 'radiobutton-checked',
        },
        {
            useCase: 'select',
            type: 'checkbox',
            displayName: 'TrueFalse',
            icon: 'accept',
        },
        {
            useCase: 'select',
            type: 'select',
            displayName: 'Auswahlfeld',
            icon: 'view-list',
        },
        {
            useCase: 'pattern',
            type: 'url',
            displayName: 'URL Feld',
            icon: 'group',
        },
        {
            useCase: 'pattern',
            type: 'email',
            displayName: 'E-Mail Feld',
            icon: 'mail',
        }
    ];

    return elements;
}
