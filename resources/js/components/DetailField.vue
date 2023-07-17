<template>
    <PanelItem :field="field">
        <template #value>
            <div v-if="field.shouldShow && hasContent">
                <div class="markdown leading-normal">
                    <FroalaView v-model:value="field.value"></FroalaView>
                </div>
            </div>

            <div v-else-if="hasContent">
                <div v-if="expanded" class="markdown leading-normal">
                    <FroalaView v-model:value="field.value"></FroalaView>
                </div>

                <a
                    v-if="!field.shouldShow"
                    aria-role="button"
                    @click="toggle"
                    class="cursor-pointer dim inline-block text-primary font-bold"
                    :class="{ 'mt-6': expanded }"
                >
                    {{ showHideLabel }}
                </a>
            </div>

            <div v-else>
                &mdash;
            </div>
        </template>
    </PanelItem>
</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

    data: () => ({ expanded: false }),

    computed: {
        hasContent() {
            return this.content !== '' && this.content !== null;
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
