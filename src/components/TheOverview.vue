<template>
    <h1>Vuetiful....</h1>
    <button @click="createDummyUserFilter">Create Random User Filter (Target Group)</button>

</template>
<script setup>
    import { ref } from 'vue';

    // TODO: The following code blocks are only for testing/dev purposes to determine the process of creating user filters!
    const availableFields = ref();
    const fetchAvailableFields = async () => {
        STUDIP.jsonapi.withPromises().get(
            'checkin-user-filter-fields', // We need to use this route! because user-filter-fields does not reflect checkin filters!
            // 'user-filter-fields',
            {
                data: {
                    filter: {
                        context: 'StudipCheckin', // TODO: we have create context if we want it correctly!
                        target: '' // student, employees, ....
                    }
                }
            }
        ).then(response => {
            availableFields.value = response.data;
        });
    };

    const userFilterFields = ref();
    const createDummyUserFilter = async () => {

        await fetchAvailableFields();
        console.log('availableFields.value', availableFields.value);
        let filter = [];
        filter.push({
            attributes: {
                // TODO: We have to create fields of our own!
                type: 'UserFilterFields\\StudipCheckin\\StudipCheckinGenderFilter',
                'compare-operator': '=',
                value: '0',
            },
        });

        STUDIP.jsonapi.withPromises().post(
            'checkin-user-filters', // We can also use user-filters and the checkin filter are registered there! at least in my local because of the bootstrap namescpace inclusion!
            // 'user-filters',
            {
                data: {
                    data: {
                        attributes: {
                            filters: filter
                        }
                    }
                }
            }
        ).then(response => {
            console.log('user-filters created!', response);
            STUDIP.Report.success('Successfully created user filter, now you can call the form creation.');
            userFilterFields.value = response.data.attributes.fields;
            createDummyForm();
        })
        .catch(error => {
            STUDIP.Report.error('There was an Error: ', error);
        });
    };

    const createDummyForm = async () => {
        STUDIP.jsonapi.withPromises().post(
            'checkin-forms',
            {
                data: {
                    data: {
                        attributes: {
                            name: 'Dummy Form',
                            structure: '{}',
                            'filter-fields': userFilterFields.value
                        }
                    }
                }
            }
        ).then(response => {
            console.log('checkin-forms created!', response);
            STUDIP.Report.success('Successfully created form.');
        })
        .catch(error => {
            STUDIP.Report.error('There was an Error: ', error);
        });
    };
</script>
