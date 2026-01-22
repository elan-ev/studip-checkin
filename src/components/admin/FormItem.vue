<template>
    <tr>
        <td>
            <input type="checkbox" :name="`form-selection-${form.id}`" :id="`form-selection-${form.id}`">
        </td>
        <td>{{ form.name }}</td>
        <td>{{ form.version }}</td>
        <td>{{ form['start-date'] }}</td>
        <td>{{ form['end-date'] }}</td>
        <td>{{ userNum }}</td>
        <td>{{ dataNum }}</td>
        <td>
            <button class="button edit" @click="redirectEdit">{{ $gettext('Edit') }}</button>
            <button class="button trash" @click="deleteForm">{{ $gettext('Delete') }}</button>
        </td>
    </tr>
</template>

<script setup>
    import { computed, defineProps } from 'vue';
    import { useFormStore } from '@/store/form';
    import { useRouter } from 'vue-router';

    const formStore = useFormStore();
    const router = useRouter();

    const props = defineProps({
        form: {
            type: Object,
            required: true,
        },
    });

    const userNum = computed(() => {
        return props.form?.['related-users']?.data?.length || 0;
    });

    const dataNum = computed(() => {
        return props.form?.['form-user-data']?.data?.length || 0;
    });

    const deleteForm = () => {
        // TODO: Add confirmation dialog and handler!
        if (confirm(`Are you sure you want to delete the form "${props.form.name}"?`)) {
            formStore.removeRecord(props.form.id, true);
        }
    };

    const redirectEdit = () => {
        router.push({ path: `/edit/${props.form.id}` });
    };
</script>
