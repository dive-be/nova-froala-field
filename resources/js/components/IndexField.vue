<template>
    <div>
        <button
            @click.stop="open"
            class="cursor-pointer text-primary-500 font-bold hover:opacity-75"
        >
            {{ showHideLabel }}
        </button>

        <Modal @close-via-escape="close" :show="expanded" size="3xl">
            <ModalHeader
                v-text="__(`${resourceName} Content`)"
                class="bg-white dark:bg-gray-800 rounded-t-lg"
            />

            <ModalContent class="bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
                <div class="fr-view-modal__content py-4">
                    <FroalaView :value="fieldValue"></FroalaView>
                </div>
            </ModalContent>

            <ModalFooter class="flex items-center ml-auto rounded-b-lg">
                <LinkButton type="button" @click.prevent="close" class="ml-auto mr-3">
                    {{ __('Close') }}
                </LinkButton>
            </ModalFooter>
        </Modal>
    </div>
</template>

<script>
export default {
    props: ['resourceName', 'field'],

    data: () => ({ expanded: false }),

    computed: {
        fieldValue() {
            return this.field.displayedAs || this.field.value;
        },

        showHideLabel() {
            return this.expanded ? this.__('Hide Content') : this.__('Show Content');
        },
    },

    methods: {
        close() {
            this.expanded = false;
        },

        open() {
            this.expanded = true;
        },
    },
};
</script>
