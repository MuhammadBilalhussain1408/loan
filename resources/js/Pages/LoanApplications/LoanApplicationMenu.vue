<template>
    <div class="bg-white p-4 shadow rounded">
        <div class="flex justify-between">
            <h3>{{ application.product.name }} (#{{ application.id }})</h3>
            <div class="flex justify-end mb-4">
                <inertia-link v-if="can('loans.index') && application.status==='disbursed' && application.loan"
                              :href="route('loans.show', application.loan.id)"
                              tabindex="-1"
                              class="inertia-link px-4 py-2 border  text-white font-semibold bg-green-600 hover:bg-green-700"
                              title="View Loan">
                    View Loan
                </inertia-link>
                <button v-if="can('loans.applications.disburse') && application.status==='approved'" title="Disburse"
                        class="px-4 py-2  text-white font-semibold bg-blue-600 hover:bg-blue-700"
                        @click="showDisburseLoanApplicationModal=true">
                    <font-awesome-icon icon="check"/>
                    Disburse
                </button>
                <inertia-link v-if="can('loans.applications.update') && application.status==='pending'"
                              :href="route('loans.applications.edit', application.id)"
                              tabindex="-1"
                              class="inertia-link  px-4 py-2 border  text-white font-semibold bg-blue-600 hover:bg-blue-700"
                              title="Edit">
                    <font-awesome-icon icon="edit"/>
                    Edit
                </inertia-link>
                <button v-if="can('loans.applications.destroy') && application.status==='pending'" title="Delete"
                        @click="confirmingDeletion=true"
                        class="px-4 py-2  text-white font-semibold bg-red-600 hover:bg-red-700">
                    <font-awesome-icon icon="trash"/>
                    Delete
                </button>
                <button title="Print"
                        @click="printSection()"
                        class="px-4 py-2 ms-1 text-white font-semibold bg-orange-500 hover:bg-orange-600">
                    <font-awesome-icon icon="print"/>
                    Print
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">
            <div>
                <div v-if="application.checklist">
                    <h4 class="font-bold text-lg">Checklist:{{ application.checklist.name }}</h4>
                    <table class="w-full border-collapse border border-gray-200">
                        <tbody>
                        <tr class="text-left" v-for="(item,index) in application.checklist_items">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">{{ item.name }}</td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500 text-center">
                                <span v-if="item.completed"><font-awesome-icon class="text-green-600"
                                                                               icon="check-circle"/> </span>
                                <span v-else><font-awesome-icon class="text-red-600" icon="times-circle"/> </span>
                                <button v-if="!item.completed  && can('loans.applications.update_checklist_items')"
                                        @click="changeChecklistItemStatus(1,item.id)"
                                        class="ml-2 text-xs rounded bg-green-600 text-white p-1">mark complete
                                </button>
                                <button
                                    v-if="item.completed && application.status==='pending' && can('loans.applications.update_checklist_items')"
                                    @click="changeChecklistItemStatus(0,item.id)"
                                    class="ml-2 text-xs rounded bg-red-600 text-white p-1">mark incomplete
                                </button>
                            </td>
                        </tr>
                        </tbody>
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
                                          v-if="application.status==='pending'">
                                        pending
                                    </span>
                                <span class="px-2 bg-blue-600 text-white rounded text-sm"
                                      v-if="application.status==='approved'">
                                        approved
                                    </span>
                                <span class="px-2 bg-green-600 text-white rounded text-sm"
                                      v-if="application.status==='disbursed'">
                                        disbursed
                                    </span>
                                <span class="px-2 bg-red-600 text-white rounded text-sm"
                                      v-if="application.status==='rejected'">
                                        rejected
                                    </span>
                            </td>
                        </tr>
                        <tr class="text-left">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Member</td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <inertia-link :href="route('members.show', application.member_id)"
                                              v-if="application.member"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ application.member.name }}
                                </inertia-link>
                            </td>
                        </tr>
                        <tr class="text-left bg-slate-50">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Currency
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <span v-if="application.currency">{{ application.currency.name }}</span>
                            </td>
                        </tr>
                        <tr class="text-left" v-if="application.loan_officer">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Loan
                                Officer
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <inertia-link :href="route('users.show', application.loan_officer_id)"
                                              v-if="application.loan_officer"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ application.loan_officer.name }}
                                </inertia-link>
                            </td>
                        </tr>
                        <tr class="text-left bg-slate-50">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Loan
                                Category
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <span v-if="application.category">{{ application.category.name }}</span>
                            </td>
                        </tr>
                        <tr class="text-left  ">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Loan
                                Designation
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <span v-if="application.designation">{{ application.designation.name }}</span>
                            </td>
                        </tr>
                        <tr class="text-left bg-slate-50">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Loan
                                Purpose
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <span v-if="application.purpose">{{ application.purpose.name }}</span>
                            </td>
                        </tr>
                        <tr class="text-left">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">Applied
                                Amount
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                <span
                                    v-if="application.approved_amount>0 && application.approved_amount!=application.applied_amount"
                                    class="mr-2">{{ $filters.formatNumber(application.approved_amount) }}</span>
                                <span
                                    :class="application.approved_amount>0 && application.approved_amount!=application.applied_amount?'line-through':''">
                                {{ $filters.formatNumber(application.applied_amount) }}
                            </span>
                            </td>
                        </tr>
                        <tr class="text-left bg-slate-50">
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                Application Date
                            </td>
                            <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                {{ $filters.date(application.created_at) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 bg-white" id="TabLinks">
        <div class="border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                <li>
                    <inertia-link :href="route('loans.applications.show',application.id)"
                                  :class="route().current('loans.applications.show')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Approval Stages
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.applications.schedules.index',application.id)"
                                  :class="route().current('loans.applications.schedules.*')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Repayment Schedule
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.applications.linked_charges.index',application.id)"
                                  :class="route().current('loans.applications.linked_charges.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Charges
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.applications.files.index',application.id)"
                                  :class="route().current('loans.applications.files.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Files
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.applications.notes.index',application.id)"
                                  :class="route().current('loans.applications.notes.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        Notes
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('loans.applications.histories.index',application.id)"
                                  :class="route().current('loans.applications.histories.index')?'text-blue-600 border-blue-600  active':'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                  class="inline-flex items-center justify-center p-4 border-b-2  rounded-t-lg group">
                        History
                    </inertia-link>
                </li>
            </ul>
        </div>
    </div>
    <jet-dialog-modal :show="showDisburseLoanApplicationModal" @close="showDisburseLoanApplicationModal = false">
        <template #title>
            Disburse Loan
        </template>

        <template #content>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <jet-label for="approved_amount" value="Approved Amount"/>
                    <jet-input type="text" id="approved_amount" class="block w-full"
                               v-model="disburseForm.approved_amount" required/>
                    <jet-input-error :message="disburseForm.errors.approved_amount"
                                     class="mt-2"/>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <jet-label for="disbursed_on_date" value="Disbursement Date"/>
                        <flat-pickr
                            v-model="disburseForm.disbursed_on_date"
                            class="form-control w-full"
                            placeholder="Select date"
                            name="date">
                        </flat-pickr>
                        <jet-input-error :message="disburseForm.errors.disbursed_on_date"
                                         class="mt-2"/>
                    </div>
                    <div>
                        <jet-label for="first_payment_date" value="First Repayment Date"/>
                        <flat-pickr
                            v-model="disburseForm.first_payment_date"
                            class="form-control w-full"
                            placeholder="Select date"
                            name="date">
                        </flat-pickr>
                        <jet-input-error :message="disburseForm.errors.first_payment_date"
                                         class="mt-2"/>
                    </div>
                    <div>
                        <jet-label for="payment_type_id" value="Payment Type"/>
                        <select-input
                            v-model="disburseForm.payment_type_id"
                            class="w-full"
                            name="payment_type_id">
                            <option v-for="item in paymentTypes" :value="item.id">{{ item.name }}</option>
                        </select-input>
                        <jet-input-error :message="disburseForm.errors.payment_type_id"
                                         class="mt-2"/>
                    </div>
                </div>
                <div>
                    <jet-label for="description" value="Notes"/>
                    <textarea-input id="description" class="mt-1 block w-full"
                                    v-model="disburseForm.description"/>
                    <jet-input-error :message="disburseForm.errors.description" class="mt-2"/>

                </div>
            </div>

        </template>

        <template #footer>
            <jet-secondary-button @click.native="showDisburseLoanApplicationModal = false">
                Cancel
            </jet-secondary-button>

            <jet-success-button class="ml-2" @click.native="disburse"
                                :class="{ 'opacity-25': disburseForm.processing }"
                                :disabled="disburseForm.processing">
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
import Button from "../../Jetstream/Button.vue";


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
        Button,
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
        application: Object,
        paymentTypes: Object,
    },
    data() {
        return {
            disburseForm: this.$inertia.form({
                approved_amount: this.application.applied_amount,
                first_payment_date: this.application.expected_first_payment_date,
                disbursed_on_date: moment().format("YYYY-MM-DD"),
                description: ``,
                payment_type_id: null,
            }),
            loanOfficerForm: this.$inertia.form({
                loan_officer_id: this.application.loan_officer_id,
                description: ``,
            }),


            usersMultiSelect: {
                placeholder: 'Search for Staff',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query || this.application.loan_officer_id)
                }
            },
            showDisburseLoanApplicationModal: false,
            showUndoStatusModal: false,
            showChangeLoanOfficerModal: false,
            showWaiveInterestModal: false,
            showRescheduleLoanModal: false,
            confirmingDeletion: false,
            processing: false,
        }
    },
    methods: {
        disburse() {
            this.disburseForm.put(this.route('loans.applications.disburse', this.application.id), {
                preserveState: false
            })
        },
        changeLoanOfficer() {
            this.loanOfficerForm.put(this.route('loans.change_loan_officer', this.application.id), {
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
            this.$inertia.delete(this.route('loans.applications.destroy', this.application.id))
            this.confirmingDeletion = false
            window.location = route('loans.applications.index')
        },
        changeChecklistItemStatus(status, id) {
            this.$swal({
                icon: 'warning',
                text: 'Are you sure?',
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No, cancel!",
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put(this.route('loans.applications.change_checklist_item_status', this.application.id), {
                        id: id,
                        status: status
                    }).then(response => {
                        this.$inertia.reload()
                        this.$swal({
                            icon: 'success',
                            text: 'Successfully updated',
                            showCancelButton: false,
                            timer: 3000
                        })
                    }).catch(error => {
                        this.$swal({
                            icon: 'error',
                            text: 'An error occurred, please try again later',
                            showCancelButton: false,
                            timer: 4000
                        })
                    })
                }
            });
        },
        printSection(){
            window.print();
        }
    },
}
</script>

<style scoped>
@media print {
  body {
    visibility: hidden;
  }
  #TabLinks{
    visibility: hidden;
  }

  button{
    visibility: hidden;
  }
  .inertia-link {
    visibility: hidden
  }
  /* #printDiv {
    visibility: visible;
    position: absolute;
    left: 0;
    top: 0;
  } */
}

</style>
