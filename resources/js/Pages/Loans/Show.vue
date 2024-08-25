<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">
                    Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Loan #{{ loan.id }}
            </h2>
        </template>

        <div class=" mx-auto">
            <loan-menu :loan="loan" :payment-types="paymentTypes"></loan-menu>
            <div class="p-4 bg-white">
                <table class="w-full border-collapse border border-gray-200">
                    <tbody>
                    <tr class="text-left bg-slate-50">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">Loan Transaction
                            Processing Strategy
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                                <span
                                    v-if="loan.transaction_processing_strategy">{{
                                        loan.transaction_processing_strategy.name
                                    }}</span>
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">Loan Term
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            {{ loan.loan_term }}
                            <span>{{ loan.repayment_frequency_type }}</span>
                        </td>
                    </tr>
                    <tr class="text-left bg-slate-50">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">Repayments
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            Every {{ loan.repayment_frequency }}
                            <span>{{ loan.repayment_frequency_type }}</span>
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">Interest
                            Methodology
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            <span v-if="loan.interest_methodology==='flat'">Flat</span>
                            <span v-if="loan.interest_methodology==='declining_balance'">Declining Balance</span>
                        </td>
                    </tr>
                    <tr class="text-left bg-slate-50">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">Interest
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            {{ loan.interest_rate }}% per {{ loan.interest_rate_type }}
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">Grace On
                            Principal Payment
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            {{ loan.grace_on_principal_paid }}
                        </td>
                    </tr>
                    <tr class="text-left bg-slate-50">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">Grace On
                            Interest Payment
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            {{ loan.grace_on_interest_paid }}
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">Grace On
                            Interest Charged
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            {{ loan.grace_on_interest_charged }}
                        </td>
                    </tr>

                    <tr class="text-left bg-slate-50" v-if="loan.disbursed_on_date">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            Disbursed On
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            {{ loan.disbursed_on_date }}
                        </td>
                    </tr>
                    <tr class="text-left bg-slate-50" v-for="item in loan.custom_fields">
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            {{ item.name }}
                        </td>
                        <td class="border border-gray-200 px-6 pt-4 pb-4 font-medium text-gray-500">
                            {{ item.field_value }}
                        </td>
                    </tr>
                    </tbody>
                </table>
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
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import JetDropdown from '@/Jetstream/Dropdown.vue'
import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import LoanMenu from './LoanMenu.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    props: {
        loan: Object,
        paymentTypes: Object,
    },
    components: {
        FontAwesomeIcon,
        SelectInput,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,
        JetDropdown,
        JetDropdownLink,
        JetDialogModal,
        JetSecondaryButton,
        JetSuccessButton,
        JetConfirmationModal,
        JetDangerButton,
        LoanMenu,

    },
    data() {
        return {
            showChangeStatusModal: false,
            showRejectLoanModal: false,
            showWithdrawLoanModal: false,
            showDisburseLoanModal: false,
            confirmingDeletion: false,
            selectedPaymentRecord: null,
            selectedRecord: null,
            processing: false,
            action: '',
            pageTitle: "Loan Details",
            pageDescription: "Loan Details",
        }

    },
    mounted() {

    },
    methods: {


    }
}
</script>
<style scoped>

</style>
