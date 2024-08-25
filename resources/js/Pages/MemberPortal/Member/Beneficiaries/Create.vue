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
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Beneficiary</h2>
                        <inertia-link class="btn btn-blue" :href="route('portal.member.beneficiaries.index')">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <jet-label for="title_id" value="Title"/>
                                    <Multiselect
                                        id="title_id"
                                        v-model="form.title_id"
                                        value-prop="id"
                                        label="name"
                                        :options="titles"
                                    />
                                    <jet-input-error :message="form.errors.title_id" class="mt-2"/>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div>
                                        <jet-label for="first_name" value="First Name"/>
                                        <jet-input id="first_name" type="text" class="block w-full"
                                                   v-model="form.first_name"
                                                   required
                                                   autofocus autocomplete="first_name"/>
                                        <jet-input-error :message="form.errors.first_name" class="mt-2"/>
                                    </div>
                                    <div class="">
                                        <jet-label for="middle_name" value="Middle Name"/>
                                        <jet-input id="middle_name" type="text" class=" block w-full"
                                                   v-model="form.middle_name"
                                                   autocomplete="middle_name"/>
                                        <jet-input-error :message="form.errors.middle_name" class="mt-2"/>
                                    </div>
                                    <div class="">
                                        <jet-label for="last_name" value="Last Name"/>
                                        <jet-input id="last_name" type="text" class="block w-full"
                                                   v-model="form.last_name"
                                                   required autocomplete="last_name"/>
                                        <jet-input-error :message="form.errors.last_name" class="mt-2"/>
                                    </div>
                                </div>
                                <div>
                                    <jet-label for="gender" value="Gender"/>
                                    <select
                                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                        name="gender" v-model="form.gender" id="gender">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <jet-input-error :message="form.errors.gender" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="member_relationship_id" value="Relationship To Member"/>
                                    <Multiselect
                                        id="member_relationship_id"
                                        v-model="form.member_relationship_id"
                                        mode="single"
                                        value-prop="id"
                                        label="name"
                                        :searchable="true"
                                        :options="$page.props.memberRelationships"
                                    />
                                    <jet-input-error :message="form.errors.member_relationship_id" class="mt-2"/>
                                </div>

                                <div>
                                    <jet-label for="shares" value="% shares"/>
                                    <jet-input id="shares" type="text" class="mt-1 block w-full" v-model="form.shares"/>
                                    <jet-input-error :message="form.errors.shares" class="mt-2"/>

                                </div>
                                <div>
                                    <jet-label for="contact_number" value="Contact Number"/>
                                    <jet-input id="contact_number" type="text" class="block w-full"
                                               v-model="form.contact_number"/>
                                    <jet-input-error :message="form.errors.contact_number" class="mt-2"/>

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
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import MemberMenu from '@/Pages/Members/MemberMenu.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

export default {
    components: {
        AppLayout,

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
        TextareaInput,
    },
    props: {
        member: Object,
        countries: Object,
        memberRelationships: Object,
        titles: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                first_name: null,
                middle_name: null,
                last_name: null,
                member_relationship_id: null,
                country_id: null,
                title_id: null,
                state: null,
                city: null,
                profession_id: null,
                identification_number: null,
                address: null,
                contact_number: null,
                home_number: null,
                postal_address: null,
                dob: null,
                shares: null,
                email: null,
                gender: null,
                photo: null,
                notes: null,
            }),
            pageTitle: "Create Member Beneficiary",
            pageDescription: "Create Member Beneficiary",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('portal.member.beneficiaries.store'), {})
        },
    },
}
</script>

<style scoped>

</style>
