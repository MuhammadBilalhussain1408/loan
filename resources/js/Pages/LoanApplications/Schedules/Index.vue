<template>
    <app-layout>
        <template #header class="print:hidden">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.applications.index')">
                    Loan Applications
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Loan Application #{{ application.id }}
                <span class="text-indigo-400 font-medium">/</span>
                Repayment Schedule
            </h2>
        </template>

        <div class=" mx-auto">
            <loan-application-menu class="print:hidden" :application="application" :payment-types="paymentTypes"></loan-application-menu>
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <div class="overflow-x-auto">
                    <table class="pretty displayschedule" id="repaymentschedule" style="margin-top: 20px;">
                        <thead>

                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Date</th>
                            <th scope="col">Principal Due</th>
                            <th class="righthighlightcolheader"
                                scope="col">Principal Balance
                            </th>

                            <th class="lefthighlightcolheader"
                                scope="col">Interest Due
                            </th>
                            <th scope="col">Fees</th>
                            <th scope="col">Total Due</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td scope="row"></td>
                            <td>{{ loan_details["disbursement_date"] }}</td>
                            <td class="lefthighlightcolheader">
                                {{ $filters.formatNumber(loan_details["principal"], loan_details["decimals"]) }}
                            </td>
                            <td class="righthighlightcolheader">
                                {{ $filters.formatNumber(loan_details["principal"], loan_details["decimals"]) }}
                            </td>
                            <td class="lefthighlightcolheader"></td>
                            <td>{{ $filters.formatNumber(loan_details["disbursement_fees"], loan_details["decimals"]) }}</td>
                            <td>{{ $filters.formatNumber(loan_details["disbursement_fees"], loan_details["decimals"]) }}</td>
                        </tr>
                        <tr v-for="(item,index) in schedules">
                            <td scope="row">{{ index+1 }}</td>
                            <td>{{ item['due_date'] }}</td>
                            <td>{{ $filters.formatNumber(item['principal'], loan_details["decimals"]) }}</td>
                            <td class="righthighlightcolheader">
                                {{ $filters.formatNumber(item['balance'], loan_details["decimals"]) }}
                            </td>
                            <td class="lefthighlightcolheader">
                                {{ $filters.formatNumber(item['interest'], loan_details["decimals"]) }}
                            </td>
                            <td>{{ $filters.formatNumber(item['fees'], loan_details["decimals"]) }}</td>
                            <td>{{ $filters.formatNumber(item['total_due'], loan_details["decimals"]) }}</td>
                        </tr>
                        </tbody>
                        <tfoot class="ui-widget-header">
                        <tr>
                            <th colspan="2">Total</th>
                            <th>{{ $filters.formatNumber(loan_details["principal"], loan_details["decimals"]) }}</th>
                            <th class="righthighlightcolheader">&nbsp;</th>
                            <th class="lefthighlightcolheader">
                                {{ $filters.formatNumber(loan_details['total_interest'], loan_details["decimals"]) }}
                            </th>
                            <th>{{ $filters.formatNumber(loan_details["total_fees"], loan_details["decimals"]) }}</th>
                            <th>{{ $filters.formatNumber(loan_details["total_due"], loan_details["decimals"]) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="print:hidden flex items-center justify-end mt-4">

                    <jet-button class="ml-4" @click="print">
                        Print
                    </jet-button>
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
import Pagination from '@/Jetstream/Pagination.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import LoanApplicationMenu from "../LoanApplicationMenu.vue";

export default {
    props: {
        application: Object,
        schedules: Object,
        loan_details: Object,
        paymentTypes: Object,
    },
    components: {
        LoanApplicationMenu,
        Pagination,
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

    },
    data() {
        return {
            confirmingDeletion: false,
            selectedRecord: null,
            processing: false,
            action: '',
            pageTitle: "Loan Schedule",
            pageDescription: "Loan Schedule",
        }

    },
    mounted() {

    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        print() {
            window.print()

        },
    }
}
</script>
<style scoped>

</style>
