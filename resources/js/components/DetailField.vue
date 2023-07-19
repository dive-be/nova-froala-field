<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div v-if="field.shouldShow && hasContent">
                <div class="markdown leading-normal">
                    <FroalaView :value="field.value"></FroalaView>
                </div>
            </div>

            <div v-else-if="hasContent">
                <div v-if="expanded" class="markdown leading-normal">
                    <FroalaView :value="field.value"></FroalaView>
                </div>

                <button
                    v-if="!field.shouldShow"
                    @click.stop="toggle"
                    class="cursor-pointer text-primary-500 font-bold hover:opacity-75"
                >
                    {{ showHideLabel }}
                </button>
            </div>

            <p v-else>—</p>
        </template>
    </PanelItem>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    data: () => ({ expanded: false }),

    computed: {
        hasContent() {
            return this.field.value !== '' && this.field.value !== null;
        },

        showHideLabel() {
            return !this.expanded ? this.__('Show Content') : this.__('Hide Content');
        },
    },

    methods: {
        toggle() {
            this.expanded = ! this.expanded;
        },
    },
};
</script>
