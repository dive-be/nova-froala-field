<template>
    <DefaultField :errors="errors" :field="field" :full-width-content="true" @keydown.native.stop>
        <template #field>
            <Froala
                :id="field.name"
                v-model:value="value"
                tag="textarea"
                :config="options"
                :placeholder="field.name"
            ></Froala>
        </template>
    </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';

import MediaConfigurator from '../MediaConfigurator';

export default {
    mixins: [HandlesValidationErrors, FormField],

    data() {
        return {
            mediaConfigurator: new MediaConfigurator(this.resourceName, this.field),
        };
    },

    computed: {
        options() {
            return _.merge(window.froala || {}, this.field.options, this.defaultConfig());
        },
    },

    beforeDestroy() {
        this.mediaConfigurator.cleanUp();
    },

    mounted() {
        if (typeof window.froala !== 'undefined' && typeof window.froala.events !== 'undefined') {
            _.forEach(window.froala.events, value => {
                value.apply(this);
            });
        }
    },

    methods: {
        fill(formData) {
            formData.append(this.field.attribute, this.value || '');
            formData.append(this.field.attribute + 'DraftId', this.field.draftId);
        },

        /**
         * Additional configurations
         */
        defaultConfig() {
            return this.mediaConfigurator.getConfig();
        },
    },
};
</script>
