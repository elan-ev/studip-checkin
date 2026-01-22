export function useInputElements() {
    // TODO all configs, naming, more/better attributes?

    elements = [ 
        {
            useCase: 'text',
            type: 'text',
            icon: '',
        },
        {
            useCase: 'select',
            type: 'radio',
            icon: '',
        },
        {
            useCase: 'select',
            type: 'select',
            icon: '',
        },
        {
            useCase: 'pattern',
            type: 'url',
            icon: '',
        },
        {
            useCase: 'pattern',
            type: 'email',
            icon: '',
        }
    ];

    return elements;
}