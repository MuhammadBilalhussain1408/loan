<template>
    <div class=" bg-white p-4 shadow rounded">
        <div class="flex justify-between">
            <h3>{{ loan.product.name }} (#{{ loan.id }})</h3>
            <div class="flex justify-end mb-4">
                <inertia-link v-if="loan.status==='active'"
                              :href="route('portal.loans.transactions.create', loan.id)"
                              tabindex="-1"
                              class="px-4 py-2 text-white font-semibold bg-blue-600 hover:bg-blue-700"
                              title="Add Repayment">
                    <font-awesome-icon icon="dollar-sign"/>
                    Pay Online
                </inertia-link>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">
            <div>
                <div
                    v-if="loan.status==='submitted' || loan.status==='approved' || loan.status==='rejected' ||  loan.status==='written_off' || loan.status==='withdrawn'">
                    <div
                        class="px-2 w-full md:h-64 flex justify-center items-center bg-orange-600 text-white rounded text-sm "
                        v-if="loan.status==='submitted'">
                        <span class="font-bold text-xl">Pending Approval</span>
                    </div>
                    <div
                        class="px-2 w-full md:h-64 flex justify-center items-center bg-yellow-600 text-white rounded text-sm"
                        v-if="loan.status==='approved'">
                        <span class="font-bold text-xl">Awaiting Disbursement</span>
                    </div>
                    <div
                        class="px-2 w-full md:h-64 flex justify-center items-center bg-red-600 text-white rounded text-sm"
                        v-if="loan.status==='rejected'">
                        <span class="font-bold text-xl">Rejected</span>
                    </div>
                    <div
                        class="px-2 w-full md:h-64 flex justify-center items-center bg-red-600 text-white rounded text-sm"
                        v-if="loan.status==='withdrawn'">
                        <span class="font-bold text-xl">Withdrawn</span>
                    </div>
                    <div
                        class="px-2 w-full md:h-64 flex justify-center items-center bg-red-600 text-white rounded text-sm"
                        v-if="loan.status==='written_off'">
                        <span class="font-bold text-xl">Written Off</span>
                    </div>
                </div>
                <div v-if="loan.status==='active'||loan.status==='closed'||loan.status==='rescheduled'">
                    <h4 class="text-xl">Balance: <b>{{ $filters.currency(loan.total_outstanding_derived) }}</b></h4>
                    <h4 class="text-xl">
                        Timely Repayments
                        :
                        <b> {{ loan.timely_repayments }}%</b></h4>
                    <h4 class="text-xl">
                        Amount In Arrears
                        :
                        <b :class="loan.arrears_amount>0?'text-red-600':''">{{
                                $filters.currency(loan.arrears_amount)
                            }}</b></h4>
                    <h4 class="text-xl">
                        Days In Arrears:
                        <b :class="loan.arrears_days>0?'text-red-600':''">{{ $filters.currency(loan.arrears_days) }}</b>
                    </h4>
                    <table id="summarytable" class="mt-4 pretty displayschedule">
                        <thead>
                        <tr>
                            <th class="empty"></th>
                            <th>Contract</th>
                            <th>Paid</th>
                            <th>Waived</th>
                            <th>Written off</th>
                            <th>Outstanding</th>
                            <th>Overdue</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th>Principal</th>
                            <td>{{ $filters.currency(loan.principal_disbursed_derived) }}</td>
                            <td>{{ $filters.currency(loan.principal_repaid_derived) }}</td>
                            <td>0</td>
                            <td>{{ $filters.currency(loan.principal_written_off_derived) }}</td>
                            <td>{{ $filters.currency(loan.principal_outstanding_derived) }}</td>
                            <td>{{ $filters.currency(loan.principal_overdue) }}</td>
                        </tr>
                        <tr>
                            <th>Interest</th>
                            <td>{{ $filters.currency(loan.interest_disbursed_derived) }}</td>
                            <td>{{ $filters.currency(loan.interest_repaid_derived) }}</td>
                            <td>{{ $filters.currency(loan.interest_waived_derived) }}</td>
                            <td>{{ $filters.currency(loan.interest_written_off_derived) }}</td>
                            <td>{{ $filters.currency(loan.interest_outstanding_derived) }}</td>
                            <td>{{ $filters.currency(loan.interest_overdue) }}</td>
                        </tr>
                        <tr>
                            <th>Fees</th>
                            <td>{{ $filters.currency(loan.fees_disbursed_derived) }}</td>
                            <td>{{ $filters.currency(loan.fees_repaid_derived) }}</td>
                            <td>{{ $filters.currency(loan.fees_waived_derived) }}</td>
                            <td>{{ $filters.currency(loan.fees_written_off_derived) }}</td>
                            <td>{{ $filters.currency(loan.fees_outstanding_derived) }}</td>
                            <td>{{ $filters.currency(loan.fees_overdue) }}</td>
                        </tr>
                        <tr>
                            <th>Penalties</th>
                            <td>{{ $filters.currency(loan.penalties_disbursed_derived) }}</td>
                            <td>{{ $filters.currency(loan.penalties_repaid_derived) }}</td>
                            <td>{{ $filters.currency(loan.penalties_waived_derived) }}</td>
                            <td>{{ $filters.currency(loan.penalties_written_off_derived) }}</td>
                            <td>{{ $filters.currency(loan.penalties_outstanding_derived) }}</td>
                            <td>{{ $filters.currency(loan.penalties_overdue) }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>{{ $filters.currency(loan.total_disbursed_derived) }}</th>
                            <th>{{ $filters.currency(loan.total_repaid_derived) }}</th>
                            <th>{{ $filters.currency(loan.total_waived_derived) }}</th>
                            <th>{{ $filters.currency(loan.total_written_off_derived) }}</th>
                            <th>{{ $filters.currency(loan.total_outstanding_derived) }}</th>
                            <th>{{ $filters.currency(loan.arrears_amount) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div>

                <div>
                    <table class="w-full border-collapse border border-gray-200">
                        <tbody>
                        <tr class="text-left bg-slate-50">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Status</td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                    <span class="px-2 bg-orange-600 text-white rounded text-sm"
                                          v-if="loan.status==='submitted'">
                                        pending approval
                                    </span>
                                <span class="px-2 bg-yellow-600 text-white rounded text-sm"
                                      v-if="loan.status==='approved'">
                                        awaiting disbursement
                                    </span>
                                <span class="px-2 bg-blue-600 text-white rounded text-sm"
                                      v-if="loan.status==='active'">
                                        active
                                    </span>
                                <span class="px-2 bg-red-600 text-white rounded text-sm"
                                      v-if="loan.status==='rejected'">
                                        rejected
                                    </span>
                                <span class="px-2 bg-red-600 text-white rounded text-sm"
                                      v-if="loan.status==='withdrawn'">
                                       withdrawn
                                    </span>
                                <span class="px-2 bg-red-600 text-white rounded text-sm"
                                      v-if="loan.status==='written_off'">
                                        written off
                                    </span>
                                <span class="px-2 bg-green-600 text-white rounded text-sm"
                                      v-if="loan.status==='closed'">
                                        closed
                                    </span>
                                <span class="px-2 bg-orange-600 text-white rounded text-sm"
                                      v-if="loan.status==='pending_reschedule'">
                                        pending reschedule
                                    </span>
                                <span class="px-2 bg-blue-600 text-white rounded text-sm"
                                      v-if="loan.status==='rescheduled'">
                                        rescheduled
                                    </span>
                                <span class="px-2 bg-orange-600 text-white rounded text-sm"
                                      v-if="loan.status==='overpaid'">
                                        overpaid
                                    </span>
                            </td>
                        </tr>
                        <tr class="text-left bg-slate-50">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Currency
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <span v-if="loan.currency">{{ loan.currency.name }}</span>
                            </td>
                        </tr>
                        <tr class="text-left">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Loan
                                Officer
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <span
                                    v-if="loan.loan_officer"
                                    tabindex="-1">
                                    {{ loan.loan_officer.name }}
                                </span>
                            </td>
                        </tr>
                        <tr class="text-left bg-slate-50">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Loan
                                Purpose
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <span v-if="loan.purpose">{{ loan.purpose.name }}</span>
                            </td>
                        </tr>
                        <tr class="text-left bg-slate-50">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Principal
                                Amount
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                {{ $filters.formatNumber(loan.principal) }}
                            </td>
                        </tr>
                        <tr class="text-left" v-if="loan.status==='active' ||loan.status==='closed'">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                Disbursement Date
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                {{ loan.disbursed_on_date }}
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 bg-white">
        <div class="border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                <li>
                    <inertia-link :href="route('portal.loans.show',loan.id)"
                                  :class="route().current('portal.loans.show')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Account Details
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('portal.loans.schedules.index',loan.id)"
                                  v-if="loan.status==='active'||loan.status==='closed'||loan.status==='rescheduled'"
                                  :class="route().current('portal.loans.schedules.*')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Repayment Schedule
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('portal.loans.transactions.index',loan.id)"
                                  v-if="loan.status==='active'||loan.status==='closed'||loan.status==='rescheduled'"
                                  :class="route().current('portal.loans.transactions.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Transactions
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('portal.loans.linked_charges.index',loan.id)"
                                  :class="route().current('portal.loans.linked_charges.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Charges
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('portal.loans.files.index',loan.id)"
                                  :class="route().current('portal.loans.files.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Files
                    </inertia-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
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
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";


const fetchUsers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type_not_in=member&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + ('(#' + item.id + ')')}
    })
}
export default {

    components: {
        FontAwesomeIcon,
        SelectInput,
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
    props: {
        loan: Object,
        paymentTypes: Object,
    },
    data() {
        return {
            showChangeStatusModal: false,
            showUndoStatusModal: false,
            showChangeLoanOfficerModal: false,
            showWaiveInterestModal: false,
            showRescheduleLoanModal: false,
            confirmingDeletion: false,
            processing: false,
        }
    },
    methods: {},
}
</script>

<style scoped>

</style>
