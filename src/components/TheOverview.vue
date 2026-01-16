<template>
    <h1>Vuetiful....</h1>
    <button @click="createDummyForm"> + Create Dummy Checkin Form</button>
    <br>
    <button @click="updateDummyForm"> / Update Dummy Checkin Form</button>
    <br>
    <button @click="deleteDummyForm"> - Delete Dummy Checkin Form</button>
</template>
<script setup>
    import { ref } from 'vue';

    // TODO: The following code blocks are only for testing/dev purposes to determine the process of creating user filters!
    const availableFields = ref();
    const fetchAvailableFields = async () => {
        try {
            const response = await STUDIP.jsonapi.withPromises().get(
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
            );
            availableFields.value = response.data;
            console.log('Available Fields: ', availableFields.value);
        } catch (error) {
            STUDIP.Report.error('There was an Error (checkin-user-filter-fields)');
            console.error('There was an Error: (checkin-user-filter-fields) ', error);
        }
    };

    const userFilterFields = ref();
    const applyForUserFilter = async (filters) => {
        try {
            const response = await STUDIP.jsonapi.withPromises().post(
                // 'checkin-user-filters', // We can also use user-filters and the checkin filter are registered there! at least in my local because of the bootstrap namescpace inclusion!
                'user-filters',
                {
                    data:{
                        data:  {
                            attributes: {
                                filters: filters
                            }
                        }
                    }
                }
            );

            userFilterFields.value = response.data.attributes.fields;
        } catch (error) {
            STUDIP.Report.error('There was an Error (checkin-user-filters)');
            console.error('There was an Error: (checkin-user-filters) ', error);
        }
    };

    const createDummyUserFilter = async (compareOp = '=', value = '0') => {
        await fetchAvailableFields();
        let filters = [];
        filters.push({
            attributes: {
                // TODO: We have to create fields of our own!
                type: 'UserFilterFields\\StudipCheckin\\StudipCheckinGenderFilter',
                'compare-operator': compareOp,
                value: value,
            },
        });
        await applyForUserFilter(filters);
        console.log('User Filter Fields: ', userFilterFields.value);
    };

    const formId = ref();
    const createDummyForm = async () => {
        await createDummyUserFilter();
        const structure = [
            {
                type: 'checkin-form-element-checkbox',
                attributes: {
                    label: 'I agree to the terms and conditions?',
                    required: true,
                    values: {
                        true: 'Agree',
                        false: 'Disagree'
                    }
                }
            }
        ];
        STUDIP.jsonapi.withPromises().post(
            'checkin-forms?include=related-users,user-filter,form-user-data',
            {
                data: {
                    data: {
                        attributes: {
                            name: 'Dummy Form',
                            structure: structure,
                            'filter-fields': userFilterFields.value
                        }
                    }
                }
            }
        ).then(response => {
            console.log('checkin-forms created!', response);
            STUDIP.Report.success('Successfully created form.');
            formId.value = response.data.id;
        })
        .catch(error => {
            STUDIP.Report.error('There was an Error (checkin-forms) Creation!');
            console.error('There was an Error: (checkin-forms) Creation!', error);
        });
    };

    const updateDummyForm = async () => {
        const fid = formId.value;
        if (!fid) {
            STUDIP.Report.error('No Form ID available for update. Please create a form first.');
            return;
        }
        // For update, first get the existing form.
        let existingForm = await getForm(fid);
        console.log('checkin-form show!', existingForm);
        STUDIP.Report.success('Successfully form with ID: ' + fid);

        await createDummyUserFilter('=', '1');

        const structure = existingForm.data.attributes.structure;
        structure.push(
            {
                type: 'checkin-form-element-text',
                attributes: {
                    label: 'Please enter your full name:',
                    required: true,
                    placeholder: 'Full Name'
                }
            }
        );

        STUDIP.jsonapi.withPromises().patch(
            `checkin-forms/${fid}`,
            {
                data: {
                    data: {
                        attributes: {
                            name: 'Dummy Form (Edited)',
                            structure: structure,
                            'filter-fields': userFilterFields.value
                        }
                    }
                }
            }
        ).then(response => {
            console.log('checkin-forms updated!', response);
            STUDIP.Report.success('Successfully updated form.');
        })
        .catch(error => {
            STUDIP.Report.error('There was an Error (checkin-forms) Update!');
            console.error('There was an Error: (checkin-forms) Update!', error);
        });
    }

    const getForm = async (formId) => {
        return STUDIP.jsonapi.withPromises().get(
            `checkin-forms/${formId}`,
        ).catch(error => {
            STUDIP.Report.error('There was an Error (checkin-forms) Fetching!');
            console.error('There was an Error: (checkin-forms) Fetching!', error);
        });
    };

    const deleteDummyForm = async () => {
        const fid = formId.value;
        if (!fid) {
            STUDIP.Report.error('No Form ID available for deletion. Please create a form first.');
            return;
        }
        STUDIP.jsonapi.withPromises().delete(
            `checkin-forms/${fid}`,
        ).then(response => {
            console.log('checkin-forms deleted!', response);
            STUDIP.Report.success('Successfully deleted form.');
            formId.value = null;
        })
        .catch(error => {
            STUDIP.Report.error('There was an Error (checkin-forms) Deletion!');
            console.error('There was an Error: (checkin-forms) Deletion!', error);
        });
    };
</script>
