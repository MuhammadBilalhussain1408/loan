<template>
    <div class=" bg-white p-4 shadow rounded">
        <div class="flex justify-between">
            <h3>{{ loan.product.name }} (#{{ loan.id }})</h3>
            <div class="flex justify-end mb-4">
                <inertia-link v-if="can('loans.transactions.create') && loan.status==='active'"
                              :href="route('loans.transactions.create', loan.id)"
                              tabindex="-1"
                              class="px-4 py-2   text-white font-semibold bg-blue-600 hover:bg-blue-700"
                              title="Add Repayment">
                    <font-awesome-icon icon="dollar-sign"/>
                    Add Repayment
                </inertia-link>
                <button v-if="can('loans.approve_loan') && loan.status==='submitted'" title="Approve"
                        @click="statusAction('approved')"
                        class="px-4 py-2  text-white font-semibold bg-blue-600 hover:bg-blue-700">
                    <font-awesome-icon icon="check"/>
                    Approve
                </button>
                <button v-if="can('loans.approve_loan') && loan.status==='submitted'" title="Reject"
                        @click="statusAction('rejected')"
                        class="px-4 py-2  text-white font-semibold bg-red-600 hover:bg-red-700">
                    <font-awesome-icon icon="times"/>
                    Reject
                </button>
                <button v-if="can('loans.approve_loan') && loan.status==='submitted'" title="Withdraw"
                        @click="statusAction('withdrawn')"
                        class="px-4 py-2  text-white font-semibold bg-orange-600 hover:bg-orange-700">
                    <font-awesome-icon icon="times"/>
                    Withdraw
                </button>
                <button v-if="can('loans.disburse_loan') && loan.status==='approved'" title="Disburse"
                        @click="statusAction('active')"
                        class="px-4 py-2  text-white font-semibold bg-green-600 hover:bg-green-700">
                    <font-awesome-icon icon="flag"/>
                    Disburse
                </button>
                <button v-if="can('loans.approve_loan') && loan.status==='approved'" title="Disburse"
                        @click="undoStatusAction('undo_approval')"
                        class="px-4 py-2  text-white font-semibold bg-orange-600 hover:bg-orange-700">
                    <font-awesome-icon icon="undo"/>
                    Undo Approval
                </button>
                <button v-if="can('loans.approve_loan') && loan.status==='rejected'" title="Undo Rejection"
                        @click="undoStatusAction('undo_rejection')"
                        class="px-4 py-2  text-white font-semibold bg-blue-600 hover:bg-blue-700">
                    <font-awesome-icon icon="undo"/>
                    Undo Rejection
                </button>
                <button v-if="can('loans.transactions.update') && loan.status==='active'" title="Waive Interest"
                        @click="showWaiveInterestModal=true"
                        class="px-4 py-2  text-white font-semibold bg-red-600 hover:bg-red-700">
                    <font-awesome-icon icon="minus"/>
                    Waive Interest
                </button>
                <button v-if="can('loans.disburse_loan') && loan.status==='active'" title="Undo Disbursement"
                        @click="undoStatusAction('undo_disbursement')"
                        class="hidden px-4 py-2  text-white font-semibold bg-orange-600 hover:bg-orange-700">
                    <font-awesome-icon icon="undo"/>
                    Undo Disbursement
                </button>
                <button
                    v-if="can('loans.update') && (loan.status==='submitted'||loan.status==='approved'||loan.status==='active')"
                    title="Change Loan Officer"
                    @click="showChangeLoanOfficerModal=true"
                    class="px-4 py-2  text-white font-semibold bg-blue-600 hover:bg-blue-700">
                    <font-awesome-icon icon="user"/>
                    Change Loan Officer
                </button>
                <button v-if="can('loans.write_off_loan') && loan.status==='active'" title="Write off Loan"
                        @click="statusAction('written_off')"
                        class="hidden px-4 py-2  text-white font-semibold bg-red-600 hover:bg-red-700">
                    <font-awesome-icon icon="times"/>
                    Write Off
                </button>
                <inertia-link v-if="can('loans.update') && loan.status==='submitted'"
                              :href="route('loans.edit', loan.id)"
                              tabindex="-1"
                              class="hidden px-4 py-2 border  text-white font-semibold bg-blue-600 hover:bg-blue-700"
                              title="Edit">
                    <font-awesome-icon icon="edit"/>
                    Edit
                </inertia-link>
                <button v-if="can('loans.reschedule_loan') && loan.status==='active'" title="Reschedule Loan"
                        @click="showRescheduleLoanModal=true"
                        class="hidden px-4 py-2  text-white font-semibold bg-blue-600 hover:bg-blue-700">
                    <font-awesome-icon icon="share"/>
                    Reschedule Loan
                </button>
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
                    <h4 class="text-xl">Balance: <b>{{ $filters.formatNumber(loan.total_outstanding_derived) }}</b></h4>
                    <h4 class="text-xl">
                        Timely Repayments
                        :
                        <b> {{ loan.timely_repayments }}%</b></h4>
                    <h4 class="text-xl">
                        Amount In Arrears
                        :
                        <b :class="loan.arrears_amount>0?'text-red-600':''">{{
                                $filters.formatNumber(loan.arrears_amount)
                            }}</b></h4>
                    <h4 class="text-xl">
                        Days In Arrears:
                        <b :class="loan.arrears_days>0?'text-red-600':''">{{ $filters.formatNumber(loan.arrears_days) }}</b>
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
                            <td>{{ $filters.formatNumber(loan.principal_disbursed_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.principal_repaid_derived) }}</td>
                            <td>0</td>
                            <td>{{ $filters.formatNumber(loan.principal_written_off_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.principal_outstanding_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.principal_overdue) }}</td>
                        </tr>
                        <tr>
                            <th>Interest</th>
                            <td>{{ $filters.formatNumber(loan.interest_disbursed_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.interest_repaid_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.interest_waived_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.interest_written_off_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.interest_outstanding_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.interest_overdue) }}</td>
                        </tr>
                        <tr>
                            <th>Fees</th>
                            <td>{{ $filters.formatNumber(loan.fees_disbursed_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.fees_repaid_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.fees_waived_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.fees_written_off_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.fees_outstanding_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.fees_overdue) }}</td>
                        </tr>
                        <tr>
                            <th>Penalties</th>
                            <td>{{ $filters.formatNumber(loan.penalties_disbursed_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.penalties_repaid_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.penalties_waived_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.penalties_written_off_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.penalties_outstanding_derived) }}</td>
                            <td>{{ $filters.formatNumber(loan.penalties_overdue) }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>{{ $filters.formatNumber(loan.total_disbursed_derived) }}</th>
                            <th>{{ $filters.formatNumber(loan.total_repaid_derived) }}</th>
                            <th>{{ $filters.formatNumber(loan.total_waived_derived) }}</th>
                            <th>{{ $filters.formatNumber(loan.total_written_off_derived) }}</th>
                            <th>{{ $filters.formatNumber(loan.total_outstanding_derived) }}</th>
                            <th>{{ $filters.formatNumber(loan.arrears_amount) }}</th>
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
                        <tr class="text-left">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Member</td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <inertia-link :href="route('members.show', loan.member_id)" v-if="loan.member"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ loan.member.name }}
                                </inertia-link>
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
                                <inertia-link :href="route('users.show', loan.loan_officer_id)"
                                              v-if="loan.loan_officer"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ loan.loan_officer.name }}
                                </inertia-link>
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

                        <tr class="text-left">
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
                    <inertia-link :href="route('loans.show',loan.id)"
                                  :class="route().current('loans.show')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Account Details
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.schedules.index',loan.id)"
                                  :class="route().current('loans.schedules.*')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Repayment Schedule
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.transactions.index',loan.id)"
                                  :class="route().current('loans.transactions.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Transactions
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.linked_charges.index',loan.id)"
                                  :class="route().current('loans.linked_charges.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Charges
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.files.index',loan.id)"
                                  :class="route().current('loans.files.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Files
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.notes.index',loan.id)"
                                  :class="route().current('loans.notes.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Notes
                    </inertia-link>
                </li>
            </ul>
        </div>
    </div>
    <jet-dialog-modal :show="showChangeStatusModal" @close="showChangeStatusModal = false">
        <template #title>
            <span v-if="statusForm.status==='approved'">Approve</span>
            <span v-if="statusForm.status==='active'">Disburse</span>
            <span v-if="statusForm.status==='rejected'">Reject</span>
            <span v-if="statusForm.status==='withdrawn'">Withdraw</span>
            <span v-if="statusForm.status==='written_off'">Write off</span>
            <span v-if="statusForm.status==='reschedule'">Reschedule</span>
            Loan
        </template>

        <template #content>
            <div class="grid grid-cols-1 gap-4">
                <div v-if="statusForm.status==='approved'">
                    <jet-label for="approved_amount" value="Amount"/>
                    <jet-input type="text" id="approved_amount" class="block w-full"
                               v-model="statusForm.approved_amount" required/>
                    <jet-input-error :message="statusForm.errors.approved_amount"
                                     class="mt-2"/>
                </div>
                <div v-if="statusForm.status==='active'">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <jet-label for="disbursed_on_date" value="Actual Disbursement Date"/>
                            <flat-pickr
                                v-model="statusForm.disbursed_on_date"
                                class="form-control w-full"
                                placeholder="Select date"
                                name="date">
                            </flat-pickr>
                            <jet-input-error :message="statusForm.errors.disbursed_on_date"
                                             class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="first_payment_date" value="First Repayment Date"/>
                            <flat-pickr
                                v-model="statusForm.first_payment_date"
                                class="form-control w-full"
                                placeholder="Select date"
                                name="date">
                            </flat-pickr>
                            <jet-input-error :message="statusForm.errors.first_payment_date"
                                             class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="payment_type_id" value="Payment Type"/>
                            <select-input
                                v-model="statusForm.payment_type_id"
                                class="w-full"
                                name="payment_type_id">
                                <option v-for="item in paymentTypes" :value="item.id">{{ item.name }}</option>
                            </select-input>
                            <jet-input-error :message="statusForm.errors.payment_type_id"
                                             class="mt-2"/>
                        </div>
                    </div>
                    <div>
                        <jet-label for="approved_amount" value="Amount"/>
                        <jet-input type="text" id="approved_amount" class="block w-full"
                                   v-model="statusForm.approved_amount" required/>
                        <jet-input-error :message="statusForm.errors.approved_amount"
                                         class="mt-2"/>
                    </div>
                </div>
                <div>
                    <jet-label for="description" value="Notes"/>
                    <textarea-input id="description" class="mt-1 block w-full"
                                    v-model="statusForm.description"/>
                    <jet-input-error :message="statusForm.errors.description" class="mt-2"/>

                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click.native="showChangeStatusModal = false">
                Cancel
            </jet-secondary-button>

            <jet-success-button class="ml-2" @click.native="changeStatus"
                                :class="{ 'opacity-25': statusForm.processing }"
                                :disabled="statusForm.processing">
                Save
            </jet-success-button>
        </template>
    </jet-dialog-modal>
    <jet-dialog-modal :show="showChangeLoanOfficerModal" @close="showChangeLoanOfficerModal = false">
        <template #title>
            Change Loan Officer
        </template>

        <template #content>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <jet-label for="loan_officer_id" value="Loan Officer"/>
                    <Multiselect
                        v-model="loanOfficerForm.loan_officer_id"
                        v-bind="usersMultiSelect"
                    />
                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click.native="showChangeLoanOfficerModal = false">
                Cancel
            </jet-secondary-button>

            <jet-success-button class="ml-2" @click.native="changeLoanOfficer"
                                :class="{ 'opacity-25': loanOfficerForm.processing }"
                                :disabled="loanOfficerForm.processing">
                Save
            </jet-success-button>
        </template>
    </jet-dialog-modal>
    <jet-dialog-modal :show="showWaiveInterestModal" @close="showWaiveInterestModal = false">
        <template #title>
            Waive Interest
        </template>

        <template #content>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <jet-label for="waive_date" value="Date"/>
                    <flat-pickr
                        v-model="waiveInterestForm.date"
                        class="form-control w-full"
                        placeholder="Select date"
                        name="waive_date">
                    </flat-pickr>
                    <jet-input-error :message="waiveInterestForm.errors.date"
                                     class="mt-2"/>
                </div>
                <div>
                    <jet-label for="waived_amount" value="Amount"/>
                    <jet-input type="text" id="waived_amount" class="block w-full"
                               v-model="waiveInterestForm.amount" required/>
                    <jet-input-error :message="waiveInterestForm.errors.amount"
                                     class="mt-2"/>
                </div>
                <div>
                    <jet-label for="waive_description" value="Notes"/>
                    <textarea-input id="waive_description" class="mt-1 block w-full"
                                    v-model="waiveInterestForm.description"/>
                    <jet-input-error :message="waiveInterestForm.errors.description" class="mt-2"/>

                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click.native="showWaiveInterestModal = false">
                Cancel
            </jet-secondary-button>

            <jet-success-button class="ml-2" @click.native="waiveInterest"
                                :class="{ 'opacity-25': waiveInterestForm.processing }"
                                :disabled="waiveInterestForm.processing">
                Save
            </jet-success-button>
        </template>
    </jet-dialog-modal>
    <jet-dialog-modal :show="showRescheduleLoanModal" @close="showRescheduleLoanModal = false">
        <template #title>
            Reschedule Loan
        </template>

        <template #content>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <jet-label for="rescheduled_from_date" value="Reschedule from installment on"/>
                    <flat-pickr
                        v-model="rescheduleForm.rescheduled_from_date"
                        class="form-control w-full"
                        placeholder="Select date"
                        name="rescheduled_from_date">
                    </flat-pickr>
                    <jet-input-error :message="rescheduleForm.errors.rescheduled_from_date"
                                     class="mt-2"/>
                </div>
                <div>
                    <jet-label for="rescheduled_on_date" value="Submitted on"/>
                    <flat-pickr
                        v-model="rescheduleForm.rescheduled_on_date"
                        class="form-control w-full"
                        placeholder="Select date"
                        name="rescheduled_on_date">
                    </flat-pickr>
                    <jet-input-error :message="rescheduleForm.errors.rescheduled_on_date"
                                     class="mt-2"/>
                </div>
                <div>
                    <jet-label for="reschedule_first_payment_date">
                        <div class="flex items-center">
                            <jet-checkbox id="reschedule_first_payment_date"
                                          v-model:checked="rescheduleForm.reschedule_first_payment_date"/>
                            <div class="ml-2">
                                Change Repayment Date
                            </div>
                        </div>
                    </jet-label>
                </div>
                <div v-if="rescheduleForm.reschedule_first_payment_date">
                    <jet-label for="rescheduled_first_payment_date" value="Adjusted Due Date"/>
                    <flat-pickr
                        v-model="rescheduleForm.rescheduled_first_payment_date"
                        class="form-control w-full"
                        placeholder="Select date"
                        name="rescheduled_first_payment_date">
                    </flat-pickr>
                    <jet-input-error :message="rescheduleForm.errors.rescheduled_first_payment_date"
                                     class="mt-2"/>
                </div>
                <div>
                    <jet-label for="reschedule_adjust_loan_interest_rate">
                        <div class="flex items-center">
                            <jet-checkbox id="reschedule_adjust_loan_interest_rate"
                                          v-model:checked="rescheduleForm.reschedule_adjust_loan_interest_rate"/>
                            <div class="ml-2">
                                Adjust Interest Rate
                            </div>
                        </div>
                    </jet-label>
                </div>
                <div v-if="rescheduleForm.reschedule_adjust_loan_interest_rate">
                    <jet-label for="reschedule_interest_rate" value="Interest Rate"/>
                    <jet-input type="text" id="reschedule_interest_rate" class="block w-full"
                               v-model="rescheduleForm.reschedule_interest_rate" required/>
                    <jet-input-error :message="rescheduleForm.errors.reschedule_interest_rate"
                                     class="mt-2"/>
                </div>
                <div>
                    <jet-label for="reschedule_add_extra_installments">
                        <div class="flex items-center">
                            <jet-checkbox id="reschedule_add_extra_installments"
                                          v-model:checked="rescheduleForm.reschedule_add_extra_installments"/>
                            <div class="ml-2">
                                Add Extra Installments
                            </div>
                        </div>
                    </jet-label>
                </div>
                <div v-if="rescheduleForm.reschedule_add_extra_installments">
                    <jet-label for="reschedule_extra_installments" value="Extra Installments"/>
                    <jet-input type="text" id="reschedule_extra_installments" class="block w-full"
                               v-model="rescheduleForm.reschedule_extra_installments" required/>
                    <jet-input-error :message="rescheduleForm.errors.reschedule_extra_installments"
                                     class="mt-2"/>
                </div>
                <div>
                    <jet-label for="reschedule_enable_grace_periods">
                        <div class="flex items-center">
                            <jet-checkbox id="reschedule_enable_grace_periods"
                                          v-model:checked="rescheduleForm.reschedule_enable_grace_periods"/>
                            <div class="ml-2">
                                Introduce Grace Periods
                            </div>
                        </div>
                    </jet-label>
                </div>
                <div v-if="rescheduleForm.reschedule_enable_grace_periods">
                    <jet-label for="reschedule_grace_on_principal_paid" value="Grace On Principal Payment"/>
                    <jet-input type="text" id="reschedule_grace_on_principal_paid" class="block w-full"
                               v-model="rescheduleForm.reschedule_grace_on_principal_paid"/>
                    <jet-input-error :message="rescheduleForm.errors.reschedule_grace_on_principal_paid"
                                     class="mt-2"/>
                </div>
                <div v-if="rescheduleForm.reschedule_enable_grace_periods">
                    <jet-label for="reschedule_grace_on_interest_paid" value="Grace On Interest Payment"/>
                    <jet-input type="text" id="reschedule_grace_on_interest_paid" class="block w-full"
                               v-model="rescheduleForm.reschedule_grace_on_interest_paid"/>
                    <jet-input-error :message="rescheduleForm.errors.reschedule_grace_on_interest_paid"
                                     class="mt-2"/>
                </div>
                <div>
                    <jet-label for="rescheduled_notes" value="Notes"/>
                    <textarea-input id="rescheduled_notes" class="mt-1 block w-full"
                                    v-model="rescheduleForm.rescheduled_notes"/>
                    <jet-input-error :message="rescheduleForm.errors.rescheduled_notes" class="mt-2"/>

                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click.native="showRescheduleLoanModal = false">
                Cancel
            </jet-secondary-button>

            <jet-success-button class="ml-2" @click.native="reschedule"
                                :class="{ 'opacity-25': waiveInterestForm.processing }"
                                :disabled="waiveInterestForm.processing">
                Save
            </jet-success-button>
        </template>
    </jet-dialog-modal>
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
    <jet-confirmation-modal :show="showUndoStatusModal" @close="showUndoStatusModal = false">
        <template #title>
            Delete Record
        </template>
        <template #content>
            Are you sure you want to perform this action?
        </template>
        <template #footer>
            <jet-secondary-button @click.native="showUndoStatusModal = false">
                Nevermind
            </jet-secondary-button>

            <jet-danger-button class="ml-2" @click.native="undoChangeStatus"
                               :class="{ 'opacity-25': undoStatusForm.processing }"
                               :disabled="undoStatusForm.processing">
                Yes, undo
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>
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
            statusForm: this.$inertia.form({
                status: '',
                approved_amount: this.loan.applied_amount,
                date: null,
                first_payment_date: this.loan.expected_first_payment_date,
                disbursed_on_date: this.loan.expected_disbursement_date ? this.loan.expected_disbursement_date : moment().format("YYYY-MM-DD"),
                description: ``,
                payment_type_id: null,
            }),
            undoStatusForm: this.$inertia.form({
                status: '',
                description: ``,
            }),
            loanOfficerForm: this.$inertia.form({
                loan_officer_id: this.loan.loan_officer_id,
                description: ``,
            }),
            waiveInterestForm: this.$inertia.form({
                date: moment().format("YYYY-MM-DD"),
                amount: '',
                description: ``,
            }),
            rescheduleForm: this.$inertia.form({
                rescheduled_on_date: moment().format("YYYY-MM-DD"),
                rescheduled_from_date: '',
                rescheduled_first_payment_date: '',
                reschedule_on: 'outstanding_principal',
                rescheduled_notes: '',
                reschedule_enable_grace_periods: false,
                reschedule_adjust_loan_interest_rate: false,
                reschedule_add_extra_installments: false,
                reschedule_first_payment_date: false,
                reschedule_grace_on_principal_paid: '',
                reschedule_grace_on_interest_paid: '',
                reschedule_grace_on_interest_charged: '',
                reschedule_interest_rate: '',
                reschedule_extra_installments: '',
            }),
            usersMultiSelect: {
                placeholder: 'Search for Staff',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query || this.loan.loan_officer_id)
                }
            },
            showChangeStatusModal: false,
            showUndoStatusModal: false,
            showChangeLoanOfficerModal: false,
            showWaiveInterestModal: false,
            showRescheduleLoanModal: false,
            confirmingDeletion: false,
            processing: false,
        }
    },
    methods: {
        changeStatus() {
            this.statusForm.put(this.route('loans.change_status', this.loan.id), {
                preserveState: false
            })
        },
        undoChangeStatus() {
            this.undoStatusForm.put(this.route('loans.undo_status', this.loan.id), {
                preserveState: false
            })
        },
        changeLoanOfficer() {
            this.loanOfficerForm.put(this.route('loans.change_loan_officer', this.loan.id), {
                preserveState: false
            })
        },
        waiveInterest() {
            this.waiveInterestForm.put(this.route('loans.waive_interest', this.loan.id), {
                preserveState: false
            })
        },
        reschedule() {
            this.rescheduleForm.put(this.route('loans.reschedule_loan', this.loan.id), {
                preserveState: false
            })
        },
        statusAction(action) {
            this.statusForm.status = action
            this.showChangeStatusModal = true
        },
        undoStatusAction(action) {
            this.undoStatusForm.status = action
            this.showUndoStatusModal = true
        },
        destroy() {
            this.$inertia.delete(this.route('loans.destroy', this.loan.id))
            this.confirmingDeletion = false
            window.location = route('loans.index')
        },
    },
}
</script>

<style scoped>

</style>
