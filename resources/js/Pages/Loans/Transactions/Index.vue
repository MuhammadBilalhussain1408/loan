<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">
                    Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Loan #{{ loan.id }}
                <span class="text-indigo-400 font-medium">/</span>
                Transactions
            </h2>
        </template>

        <div class=" mx-auto">
            <loan-menu :loan="loan" :payment-types="paymentTypes"></loan-menu>
            <div class="p-4 bg-white">
                <div class="flex justify-end ">
                    <inertia-link class="btn btn-blue" v-if="loan.status==='active' && can('loans.transactions.create')"
                                  :href="route('loans.transactions.create',loan.id)">
                        <span>Add </span>
                        <span class="hidden md:inline">Repayment</span>
                    </inertia-link>
                </div>
                <div class="mt-4 relative overflow-x-auto overflow-y-visible" id="collateral">
                    <table class="w-full whitespace-no-wrap table-auto">
                        <thead class="bg-gray-50">
                        <tr class="text-left font-bold">
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Submitted on</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Transaction Type</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Debit</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Credit</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Balance</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="!results.length">
                            <td colspan="8" class="px-6 py-4 text-center">
                                No records Yet
                            </td>
                        </tr>
                        <tr v-for="result in results" :key="result.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t px-6 py-4">
                                <span>
                                {{ result.id }}
                                </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                <span>
                                {{ result.created_on }}
                                </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                <span>
                                {{ result.submitted_on }}
                                </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                <span v-if="result.type">
                                {{ result.type.name }}
                                </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                    <span>
                                        {{$filters.formatNumber(result.debit) }}

                                    </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                    <span>
                                    <!-- {{ $filters.currency(result.credit) }} -->
                                    {{ $filters.formatNumber(result.credit) }}
                                    </span>
                            </td>
                            <td class="border-t px-6 py-4">
                                    <span>
                                    {{ $filters.formatNumber(result.balance) }}
                                    </span>
                            </td>
                            <td class="border-t w-px pr-2">
                                <jet-dropdown align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black bg-gray-200 hover:bg-gray-400 hover:text-white focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="1.5"
                                                     stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-more-horizontal w-5 h-5 text-black">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="19" cy="12" r="1"></circle>
                                            <circle cx="5" cy="12" r="1"></circle>
                                        </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60 shadow-xl bg-white rounded">
                                            <jet-dropdown-link :href="route('loans.transactions.show',result.id)">
                                                <font-awesome-icon icon="eye"/>
                                                View
                                            </jet-dropdown-link>
                                            <a v-if="result.type.name==='Repayment' && !result.reversed"
                                               :href="route('loans.transactions.pdf',result.id)"
                                               class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                               target="_blank">
                                                <font-awesome-icon icon="file-pdf"/>
                                                Download Pdf
                                            </a>
                                            <a v-if="loan.status==='active' && result.type.name==='Repayment' && !result.reversed"
                                               :href="route('loans.transactions.print',result.id)"
                                               class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                               target="_blank">
                                                <font-awesome-icon icon="print"/>
                                                Print Transaction
                                            </a>
                                            <jet-dropdown-link
                                                v-if="loan.status==='active' && result.type.name==='Repayment' && !result.reversed && can('loans.transactions.update')"
                                                :href="route('loans.transactions.edit',result.id)">
                                                <font-awesome-icon icon="edit"/>
                                                Edit Transaction
                                            </jet-dropdown-link>
                                            <a href="#"
                                               v-if="loan.status==='active'&& result.reversible && can('loans.transactions.destroy')"
                                               class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                               @click="reverseAction(result.id)">
                                                <font-awesome-icon icon="undo"
                                                                   class="text-red-600 hover:text-red-900"/>
                                                Reverse
                                            </a>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <jet-confirmation-modal :show="showReverseTransactionModal" @close="showReverseTransactionModal = false">
            <template #title>
                Reverse Transaction
            </template>

            <template #content>
                Are you sure you want to reverse this transaction?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="showReverseTransactionModal = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="reverse" :class="{ 'opacity-25': processing }"
                                   :disabled="processing">
                    Reverse
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
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
import LoanMenu from '../LoanMenu.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    props: {
        loan: Object,
        results: Object,
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
        LoanMenu,

    },
    data() {
        return {
            confirmingDeletion: false,
            showReverseTransactionModal: false,
            selectedRecord: null,
            processing: false,
            action: '',
            pageTitle: "Loan Transactions",
            pageDescription: "Loan Transactions",
        }

    },
    mounted() {

    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        reverseAction(id) {
            this.showReverseTransactionModal = true
            this.selectedRecord = id
        },
        reverse() {

            this.$inertia.post(this.route('loans.transactions.reverse', this.selectedRecord), {
                preserveState: false
            })
            this.showReverseTransactionModal = false
        },
        destroy() {

            this.$inertia.delete(this.route('loans.transactions.destroy', this.selectedRecord), {
                preserveState: false
            })
            this.confirmingDeletion = false
        },
    }
}
</script>
<style scoped>

</style>
