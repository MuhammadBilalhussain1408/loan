<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('members.index')">Members
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ member.name }}
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
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Identification</h2>
                        <inertia-link class="btn btn-blue" :href="route('members.identifications.index',member.id)">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 gap-4">

                                <div>
                                    <jet-label for="member_identification_type_id" value="Type"/>
                                    <Multiselect
                                        id="member_identification_type_id"
                                        v-model="form.member_identification_type_id"
                                        mode="single"
                                        value-prop="id"
                                        label="name"
                                        :options="types"
                                    />
                                    <jet-input-error :message="form.errors.member_identification_type_id" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="identification_value" value="ID #"/>
                                    <jet-input id="identification_value" type="text" class="mt-1 block w-full"
                                               v-model="form.identification_value" required/>
                                    <jet-input-error :message="form.errors.identification_value" class="mt-2"/>

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
                                <div>
                                    <jet-label for="status" value="Status"/>
                                    <select
                                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                        name="status" v-model="form.status" id="status" required>
                                        <option :value="null"/>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                    <jet-input-error :message="form.errors.status" class="mt-2"/>
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
import AppLayout from '@/Layouts/AppLayout.vue'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import MemberMenu from '@/Pages/Members/MemberMenu.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
export default {
    components: {
        TextareaInput,
        FileInput,
        AppLayout,
        JetLabel,
        JetInput,
        JetInputError,
        SelectInput,
        JetDangerButton,
        JetSecondaryButton,
        MemberMenu,
        JetButton,
        JetCheckbox,
    },
    props: {
        member: Object,
        identification: Object,
        types: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                '_method': 'PUT',
                member_identification_type_id: this.identification.member_identification_type_id,
                identification_value: this.identification.identification_value,
                status: this.identification.status,
                file: null,
                description: this.identification.description,
            }),
            pageTitle: "Edit Member Identification",
            pageDescription: "Edit Member Identification",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('members.identifications.update', this.identification.id), {})
        },
    },
}
</script>

<style scoped>

</style>
