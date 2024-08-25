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
                Charges
            </h2>
        </template>

        <div class=" mx-auto">
            <loan-application-menu :application="application" :payment-types="paymentTypes"></loan-application-menu>
            <div class="p-4 bg-white">
                <div class="mt-4 relative overflow-x-auto overflow-y-visible" id="charges">
                    <table class="w-full whitespace-no-wrap table-auto">
                        <thead class="bg-gray-50">
                        <tr class="text-left font-bold">
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Charge Type</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Amount</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Collected on</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="!application.charges.length">
                            <td colspan="4" class="px-6 py-4 text-center">
                                No records Yet
                            </td>
                        </tr>
                        <tr v-for="result in application.charges" :key="result.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t px-6 py-4">
                                <span>
                                {{ result.name }}
                                </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                <span class="" v-if="result.charge && result.charge.option">
                                {{ $filters.formatNumber(result.amount) }}
                               <span v-if="result.charge .option" class="ml-1">
                                   <span v-if="result.charge .option.name==='Flat'"> (flat)</span>
                                   <span v-else> (% of {{ result.charge .option.name }})</span>
                               </span>
                            </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                <span>
                                {{ $filters.formatNumber(result.calculated_amount) }}
                                </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                <span v-if="result.charge && result.charge.type">
                                {{ result.charge.type.name }}
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
import LoanApplicationMenu from '../LoanApplicationMenu.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    props: {
        application: Object,
        paymentTypes: Object,
    },
    components: {
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
        LoanApplicationMenu,

    },
    data() {
        return {
            confirmingDeletion: false,
            showWaiveChargeModal: false,
            selectedRecord: null,
            processing: false,
            action: '',
            pageTitle: "Loan Application Charges",
            pageDescription: "Loan Application Charges",
        }

    },
    mounted() {

    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },

    }
}
</script>
<style scoped>

</style>
