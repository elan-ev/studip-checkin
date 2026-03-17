import { defineStore } from 'pinia';
import { ref } from 'vue';
import { CHECKIN_EXPANDED_VIEWS as expandedViewsRegistry } from
'@/components/expanded-views/expandedViewRegistery.js';

export const useDrawerStore = defineStore('drawerStore', () => {
    const isDrawerOpen = ref(false);
    const drawerComponent = ref(null);
    const drawerProps = ref({});
    const drawerAttachTarget = ref(null);
    const drawerTitle = ref('');

    function setDrawerAttachTarget() {
        const targetElement = document.querySelector('#content-wrapper');
        if (targetElement) {
            drawerAttachTarget.value = targetElement;
        } else {
            console.warn("Das Drawer-Ziel #content-wrapper wurde im DOM nicht gefunden!");
        }
    }

    function openDrawer(component, props = {}) {
            if (!component) {
            console.error('openDrawer wurde ohne Komponente aufgerufen.');
            return;
        }
        drawerComponent.value = component;
        drawerProps.value = props;
        isDrawerOpen.value = true;
    }

    function closeDrawer() {
        isDrawerOpen.value = false;
        drawerTitle.value = '';
    }

    function openUserFilterConfigInDrawer(filterId) {
        const UserFilterComponent = expandedViewsRegistry['user-filter'];
        drawerTitle.value = 'Zielgruppenfilter';
        if (!UserFilterComponent) {
            console.error('UserFilterExpandedView in Registry nicht gefunden!');
            return;
        }

        openDrawer(UserFilterComponent, {
            filterId: filterId,
        });
    }

    function openFormDataInDrawer(formId, formDataId = undefined, readOnly = false) {
        const FormDataComponent = expandedViewsRegistry['form-data'];
        if (!FormDataComponent) {
            console.error('FormDataExpandedView in Registry nicht gefunden!');
            return;
        }

        openDrawer(FormDataComponent, {
            formId,
            formDataId,
            readOnly
        });
    }

    return {
        isDrawerOpen,
        drawerComponent,
        drawerProps,
        drawerAttachTarget,
        drawerTitle,
        setDrawerAttachTarget,
        openDrawer,
        closeDrawer,

        openUserFilterConfigInDrawer,
        openFormDataInDrawer,
    };
});
