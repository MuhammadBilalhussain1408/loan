<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('members.index')">Members
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
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
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Other Loan</h2>
                        <inertia-link class="btn btn-blue" :href="route('members.beneficiaries.index', member.id)">
                            <span>Back </span>
                        </inertia-link>
                    </div>
                    <div class="mt-4">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 gap-4">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div>
                                        <jet-label for="institution" value="Institution" />
                                        <jet-input id="institution" type="text" class="block w-full"
                                            v-model="form.institution" required autofocus autocomplete="institution" />
                                        <jet-input-error :message="form.errors.institution" class="mt-2" />
                                    </div>
                                    <div class="">
                                        <jet-label for="loan_amount" value="Loan Amount" />
                                        <jet-input id="loan_amount" type="number" class=" block w-full"
                                            v-model="form.loan_amount" autocomplete="loan_amount" />
                                        <jet-input-error :message="form.errors.loan_amount" class="mt-2" />
                                    </div>
                                    <div class="">
                                        <jet-label for="monthly_installment" value="Monthly Installment" />
                                        <jet-input id="monthly_installment" type="text" class="block w-full"
                                            v-model="form.monthly_installment" required
                                            autocomplete="monthly_installment" />
                                        <jet-input-error :message="form.errors.monthly_installment" class="mt-2" />
                                    </div>
                                    <div class="">
                                        <jet-label for="status" value="Status" />
                                        <select id="status"
                                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                            v-model="form.status">
                                            <option value="Pending">Pending</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
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
        memberRelationships: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                institution: '',
                loan_amount: '',
                monthly_installment: '',
                status:''
            }),
            pageTitle: "Create Member Beneficiary",
            pageDescription: "Create Member Beneficiary",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('members.other_loan.store', this.member.id), {})
        },
    },
}
</script>

<style scoped></style>
