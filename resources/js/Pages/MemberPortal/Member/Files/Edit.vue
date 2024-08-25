<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ member.name }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 mt-20 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <member-menu :member="member"></member-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white sm:mt-4">
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit File</h2>
                        <inertia-link class="btn btn-blue" :href="route('portal.member.files.index')">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
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
            </div>
        </div>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>

</template>

<script>
import AppLayout from '@/Pages/MemberPortal/Layouts/AppLayout.vue'

import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import MemberMenu from '@/Pages/MemberPortal/Member/MemberMenu.vue'
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
        MemberMenu,
        JetButton,
        JetCheckbox,
        FileInput,
    },
    props: {
        member: Object,
        file: Object,
        types: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                '_method': 'PUT',
                file_type_id: this.file.file_type_id,
                name: this.file.name,
                description: this.file.description,
                file: null,
            }),
            pageTitle: "Edit Member File",
            pageDescription: "Edit Member File",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('portal.member.files.update', this.file.id), {})
        },
    },
}
</script>

<style scoped>

</style>
