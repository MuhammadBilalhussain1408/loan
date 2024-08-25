<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.applications.index')">
                    Loan Applications
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Loan Application #{{ application.id }}
                <span class="text-indigo-400 font-medium">/</span>
                Add File
            </h2>
        </template>

        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <jet-label for="file_type_id" value="Type"/>
                            <Multiselect
                                id="file_type_id"
                                v-model="form.file_type_id"
                                value-prop="id"
                                label="name"
                                :options="types"
                            />
                            <jet-input-error :message="form.errors.file_type_id" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="name" value="Name"/>
                            <jet-input id="name" type="text" class="mt-1 block w-full"
                                       v-model="form.name"/>
                            <jet-input-error :message="form.errors.name" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="file" value="File"/>
                            <file-input v-model="form.file" class="mt-1 block w-full" id="file" type="file"/>
                            <jet-input-error :message="form.errors.file" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="description" value="Description"/>
                            <textarea-input id="description" class="mt-1 block w-full"
                                            v-model="form.description"/>
                            <jet-input-error :message="form.errors.description" class="mt-2"/>

                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                            Save
                        </jet-button>
                    </div>
                </form>
            </div>
        </div>

        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>

</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

export default {
    components: {
        AppLayout,
        TextareaInput,
        JetLabel,
        JetInput,
        JetInputError,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        JetButton,
        JetCheckbox,
        FileInput,
    },
    props: {
        application: Object,
        types: Object,
        paymentTypes: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                file_type_id: null,
                name: null,
                description: null,
                file: null,
            }),
            pageTitle: "Create Loan Application File",
            pageDescription: "Create Loan Application File",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('loans.applications.files.store', this.application.id), {})
        },
    },
}
</script>

<style scoped>

</style>
