// src/composables/useValidation.js
import { useGettext } from 'vue3-gettext';

export default function useValidation() {
    const { $gettext } = useGettext();

    const validateField = (element, value, isVisible = true) => {
        if (!isVisible) return null;

        const payload = element.payload || {};
        const isRequired = Boolean(parseInt(payload.required));

        // 1. Pflichtfeld-Check
        const isEmpty = value === null || value === undefined || value === '' || 
                        (Array.isArray(value) && value.length === 0);
        
        if (isRequired && isEmpty) {
            return $gettext('Dieses Feld ist ein Pflichtfeld.');
        }

        if (isEmpty) return null;

        switch (element.type) {
            case 'number':
                const num = Number(value);
                if (payload.min !== undefined && num < payload.min) {
                    return $gettext('Der Wert ist zu klein.');
                }
                if (payload.max !== undefined && num > payload.max) {
                    return $gettext('Der Wert ist zu groß.');
                }
                break;
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    return $gettext('Bitte geben Sie eine gültige E-Mail-Adresse ein.');
                }
                break;
            case 'url':
                try { new URL(value); } catch (_) {
                    return $gettext('Bitte geben Sie eine gültige URL ein.');
                }
                break;
        }

        return null;
    };

    return { validateField };
}