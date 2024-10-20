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
                Repayment Schedules
            </h2>
        </template>

        <div class=" mx-auto">
            <loan-menu :loan="loan" :payment-types="paymentTypes"></loan-menu>
            <div class="p-4 bg-white">
                <div class="flex justify-start ">
                    <jet-dropdown align="left" width="60">
                        <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-800 hover:text-white focus:outline-none   transition ease-in-out duration-150">
                                                Action
                                                <font-awesome-icon class="ml-2" icon="chevron-down"/>
                                            </button>
                                        </span>
                        </template>

                        <template #content>
                            <div class="w-60 shadow-xl bg-white rounded">
                                <jet-dropdown-link
                                    v-if="loan.status==='active' && can('loans.schedules.update')"
                                    :href="route('loans.schedules.edit',loan.id)">
                                    <font-awesome-icon icon="edit"/>
                                    Edit Schedule
                                </jet-dropdown-link>

                                <a v-if="loan.status==='active' && can('loans.schedules.email')"
                                   :href="route('loans.schedules.pdf',loan.id)"
                                   class="block hidden px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                   target="_blank">
                                    <font-awesome-icon icon="envelope"/>
                                    Email Schedule
                                </a>
                                <a
                                    :href="route('loans.schedules.pdf',loan.id)"
                                    class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                    target="_blank">
                                    <font-awesome-icon icon="file-pdf"/>
                                    Download Pdf
                                </a>
                                <a
                                    :href="route('loans.schedules.print',loan.id)"
                                    class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                    target="_blank">
                                    <font-awesome-icon icon="print"/>
                                    Print Schedule
                                </a>

                            </div>
                        </template>
                    </jet-dropdown>
                </div>
                <div class="mt-4 relative overflow-x-auto overflow-y-visible" id="collateral">
                    <table id="repaymentschedule" class="pretty displayschedule" style="margin-top: 20px;">
                        <colgroup span="3"></colgroup>
                        <colgroup span="3">
                            <col class="lefthighlightcol">
                            <col>
                            <col class="righthighlightcol">
                        </colgroup>
                        <colgroup span="3">
                            <col class="lefthighlightcol">
                            <col>
                            <col class="righthighlightcol">
                        </colgroup>
                        <colgroup span="3"></colgroup>
                        <thead>
                        <tr>
                            <th scope="colgroup" colspan="5" class="empty">&nbsp;</th>
                            <th scope="colgroup" colspan="3" class="highlightcol">Loan Amount and Balance</th>
                            <th scope="colgroup" colspan="3" class="highlightcol">Total Cost of Loan</th>
                            <th scope="colgroup" colspan="1" class="empty">&nbsp;</th>
                        </tr>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Date</th>
                            <th scope="col"># Days</th>
                            <th scope="col">Paid By</th>
                            <th scope="col"></th>
                            <th scope="col" class="lefthighlightcolheader">Disbursement</th>
                            <th scope="col">Principal Due</th>
                            <th scope="col" class="righthighlightcolheader">Principal Balance</th>
                            <th scope="col" class="lefthighlightcolheader">Interest Due</th>
                            <th scope="col">Fees</th>
                            <th scope="col" class="righthighlightcolheader">Penalties

                            </th>
                            <th scope="col">Total Due</th>
                            <th scope="col">Total Paid</th>
                            <th scope="col">Total Outstanding</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td scope="row"></td>
                            <td>{{ loan.disbursed_on_date }}</td>
                            <td></td>
                            <td>
                                <span style="color: rgb(235, 36, 66);"></span>
                            </td>
                            <td>&nbsp;</td>
                            <!-- $filters.formatNumber(item['fees'], loan_details["decimals"]) -->
                            <td class="lefthighlightcolheader">{{ $filters.formatNumber(loan.principal, 2) }}</td>
                            <td></td>
                            <td class="righthighlightcolheader">{{ $filters.formatNumber(loan.principal, 2) }}</td>
                            <td class="lefthighlightcolheader"></td>
                            <td>0</td>
                            <td class="righthighlightcolheader"></td>
                            <td>0</td>
                            <td>0</td>
                            <td></td>
                        </tr>
                        <tr v-for="item in loan.schedules">
                            <td scope="row">{{ item.installment }}</td>
                            <td>{{ item.due_date }}</td>
                            <td>{{ item.days }}</td>
                            <td>
                                <span v-if="item.paid_by_date"
                                      :class="item.late_payment?'text-red-600':'text-green-600'">{{
                                        item.paid_by_date
                                    }}</span>
                                <span v-if="!item.paid_by_date && (new Date(item.due_date) < new Date())"
                                      class="text-red-600">Overdue</span>
                            </td>
                            <td>
                                <font-awesome-icon v-if="item.paid_by_date" icon="check-circle"/>
                            </td>
                            <td class="lefthighlightcolheader"></td>
                            <td>{{ $filters.formatNumber(item.principal) }}</td>
                            <td class="righthighlightcolheader">{{ $filters.formatNumber(item.balance) }}</td>
                            <td class="lefthighlightcolheader">
                                {{
                                    $filters.formatNumber(item.interest - item.interest_written_off_derived - item.interest_waived_derived)
                                }}
                            </td>
                            <td>
                                {{
                                    $filters.formatNumber(item.calculated_admin_fee - item.fees_written_off_derived - item.fees_waived_derived)
                                }}
                                <!-- {{
                                    $filters.formatNumber(item.fees - item.fees_written_off_derived - item.fees_waived_derived)
                                }} -->
                            </td>
                            <td class="righthighlightcolheader">{{
                                    $filters.currency(item.penalties - item.penalties_written_off_derived - item.penalties_waived_derived)
                                }}
                            </td>
                            <td>{{ $filters.formatNumber(item.total) }}</td>
                            <td>{{ $filters.formatNumber(item.total_paid) }}</td>
                            <td>{{ $filters.formatNumber(item.total_due) }}</td>
                        </tr>
                        </tbody>
                        <tfoot class="ui-widget-header">
                        <tr>
                            <th colspan="2">Total</th>
                            <th>{{ totalDays }}</th>
                            <th></th>
                            <th></th>
                            <th class="lefthighlightcolheader">{{ $filters.formatNumber(totalPrincipal) }}</th>
                            <th>{{ $filters.formatNumber(totalPrincipal) }}</th>
                            <th class="righthighlightcolheader">&nbsp;</th>
                            <th class="lefthighlightcolheader">{{ $filters.formatNumber(totalInterest) }}</th>
                            <th>{{ $filters.formatNumber(totalAdminFee) }}</th>
                            <th class="righthighlightcolheader">{{ $filters.formatNumber(totalPenalties) }}</th>
                            <th>{{ $filters.formatNumber(totalAmount) }}</th>
                            <th>{{ $filters.formatNumber(totalPaid) }}</th>
                            <th>{{ $filters.formatNumber(totalDue) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <jet-confirmation-modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete record?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': processing }"
                                   :disabled="processing">
                    Delete Record
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
        paymentTypes: Object,
        totalPrincipal: Number,
        totalAdminFee:Number,
        totalInterest: Number,
        totalFees: Number,
        totalPenalties: Number,
        totalPaid: Number,
        totalDue: Number,
        totalDays: Number,
        totalAmount: Number,
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
