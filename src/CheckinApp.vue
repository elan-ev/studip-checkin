<template>
    <h1>The form goes here</h1>

    <div v-if="forms">
        <h2>Pending Forms:</h2>
        <ul>
            <li v-for="form in forms" :key="form.id">
                {{ form.attributes.name }} (ID: {{ form.id }})
                <button @click="createFormUserData(form)">Create Form User Data for this Form</button>
            </li>
        </ul>
    </div>
</template>
<script setup>
    import { ref } from 'vue';

    const props = defineProps({
        userId: {
            type: String,
            required: true,
        },
    });
    // Now that we land here after the login of the user, we need to list out all active forms for the user first!
    const forms = ref();
    const getForms = () => {
        STUDIP.jsonapi.withPromises().get(
            'checkin-user-forms/' + props.userId
        ).then(response => {
            console.log('checkin-user-forms responses!', response);
            STUDIP.Report.success('User Forms fetched successfully!');
            forms.value = response.data;
        })
        .catch(error => {
            STUDIP.Report.error('There was an Error fetching user forms: ', error);
        });
    };
    getForms();

    const createFormUserData = async (form) => {
        try {
            const response = await STUDIP.jsonapi.withPromises().post(
                'checkin-form-user-data',
                {
                    data:{
                        data:  {
                            attributes: {
                                'form-id': form.id,
                                'form-version': form.attributes.version,
                                'user-id': props.userId,
                                'form-data': [ // Example form data
                                    {
                                        'type': 'text',
                                        'index': 0,
                                        'value': 'Simple text input value'
                                    }
                                ]
                            }
                        }
                    }
                }
            );

            console.log('checkin-form-user-data responses!', response);
            STUDIP.Report.success('Form User Data created successfully!');
        } catch (error) {
            STUDIP.Report.error('There was an Error (checkin-form-user-data)');
            console.error('There was an Error: (checkin-form-user-data) ', error);
        }
    }
</script>
