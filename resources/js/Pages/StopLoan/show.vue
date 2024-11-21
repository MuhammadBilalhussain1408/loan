<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.applications.index')">
                    Stop Order
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Stop Order #{{ stopLoan.id }}
            </h2>
        </template>
        <div class="bg-white rounded shadow p-6">
                <div class="text-right">
                    <button class="btn btn-primary" type="button" @click="printDiv()" id="print_btn">Print</button>
                </div>
        </div>
        <div class=" mx-auto main-div">
            <div class="bg-white rounded shadow p-6">

                <div class="grid grid-cols-1 md:grid-cols-1 gap-2 mt-4">

                        <h1><strong>Member of Parliment And Desiginated Office Bearers Pension Fund</strong></h1>
                        <table style="width: 100%;">
                            <tr>
                                <td class="p-2 font-medium text-gray-500" style="width: 50%;">
                                    <img :src="this.assetUrl + 'images/mopado-high-resolution-logo-1.png'"
                                        style="height: 200px; margin:0; padding: 0;"
                                        alt="">
                                </td>
                                <td class="p-2 font-medium text-gray-500" style="width: 50%;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td>4<sup>th</sup> Floor, Liqagha House</td>
                                        </tr>
                                        <tr>
                                            <td>P.O. Box 4639</td>
                                        </tr>
                                        <tr>
                                            <td>Office No. 401, Manzini</td>
                                        </tr>
                                        <tr>
                                            <td>Manzini Swaziland</td>
                                        </tr>
                                        <tr>
                                            <td>Nkoseluhlaza Street</td>
                                        </tr>
                                        <tr>
                                            <td>Tel:(00268) 2505 7033/4</td>
                                        </tr>
                                        <tr>
                                            <td>Fax:(00268) 2505 7034</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                </div>
                <hr />


                <div class="grid grid-cols-1 md:grid-cols-1 gap-2 mt-4">
                    <div id="printDiv">
                        <table class="w-full border-collapse border border-gray-200">
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Account Holder</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                                    stopLoan.account_holder }}
                                </td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Account Number</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                                    stopLoan.account_number }}
                                </td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Branch Code</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{ stopLoan.branch_code
                                    }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Member Account Holder Name
                                </th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                        {{ stopLoan.member_account_holder }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Member Bank Name
                                </th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                                    stopLoan.bank_name }}</td>

                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Member Account Number
                                </th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                                    stopLoan.member_account_number }}</td>

                            </tr>

                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Member Branch Code
                                </th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">
                                    {{stopLoan.member_branch_code }}</td>

                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Monthly Installment
                                </th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                                    stopLoan.monthly_installment
                                    }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Stop Order Date</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                                    stopLoan.stop_order_date }}
                                </td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Reference</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{ stopLoan.reference
                                    }}</td>
                            </tr>
                            <!-- <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Signature</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500"></td>
                            </tr> -->
                        </table>
                        <table>
                            <tr>
                                <td colspan="3">
                                    <p class="mt-5">I understand that this stop order instructions shall remain in force until advised
                                        otherwise by
                                        MOPADO only.</p>
                                </td>
                            </tr>
                            <tr>
                                <td><div class="child-div">_____________________<br>Account Holder</div></td>
                                <td><div class="child-div">_____________________<br>MOPADO</div></td>
                                <td><div style="border: 1px solid black; height: 100px; width: 150px; text-align: center; margin: 0;">Stamp
                                </div>
                                </td>
                            </tr>
                            <tr>
                                <td>

                                </td>
                                <td colspan="2">
                                    <div style="border: 1px solid black; height: 100px; width: 150px; text-align: center;">BANK
                                        STAMP</div>
                                </td>
                            </tr>
                        </table>
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
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import JetPrimaryButton from '@/Jetstream/PrimaryButton.vue'
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import JetDialogModal from '@/Jetstream/DialogModal.vue'

const fetchUsers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type_not_in=member&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return { value: item.id, label: item.name + ('(#' + item.id + ')') }
    })
}

export default {
    components: {
        TextareaInput, JetInputError, JetInput,
        JetCheckbox,
        JetDialogModal,
        FontAwesomeIcon,
        AppLayout,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        JetPrimaryButton,
        JetSuccessButton,
    },
    props: {
        stopLoan: Object,
        assetUrl: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                status: this.stopLoan.status,
            }),
            assigned_to_id: '',
            selectedStage: '',
            stage_status: '',
            stage_status_comment: '',
            processing: false,
            confirmingDeletion: false,
            showAssignModal: false,
            showAcknowledgeModal: false,
            showChangeStatusModal: false,
            selectedRecord: null,
            pageTitle: "Loan Applications",
            pageDescription: "Manage Loan Applications",
            usersMultiSelect: {
                placeholder: 'Search for Staff',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query)
                }
            },
        }
    },
    watch: {},
    methods: {
        printDiv() {
            const printDiv = document.getElementById('printDiv');
            // Create a new window object
            // const printWindow = window.open('', 'Print Window');

            // Write the contents of the div to the new window
            // printWindow.document.write('<html><head><title>Print Content</title>');
            // printWindow.document.write('<style>body{font-family:Arial,sans-serif;}</style>');
            // printWindow.document.write('</head><body>');
            // printWindow.document.write(printDiv.innerHTML);
            // printWindow.document.write('</body></html>');

            // Print the new window
            // printWindow.document.close();
            // printWindow.focus();
            window.print();

            // // Close the new window
            // printWindow.close();
        },
        changeStatus() {
            this.processing = true
            axios.put(this.route('loans.applications.change_approval_stage_status', this.application.id), {
                status: this.stage_status,
                description: this.stage_status_comment,
                id: this.selectedStage.id,
            }).then(response => {
                this.stage_status = ''
                this.stage_status_comment = ''
                this.processing = false
                this.showChangeStatusModal = false
                this.$inertia.reload()
                this.$swal({
                    icon: 'success',
                    text: 'Successfully updated',
                    showCancelButton: false,
                    timer: 3000
                })
            }).catch(error => {
                this.processing = false
                this.$swal({
                    icon: 'error',
                    text: 'An error occurred, please try again',
                    showCancelButton: false,
                    timer: 4000
                })
            })
        },
        assignApprover() {
            this.processing = true
            axios.put(this.route('loans.applications.assign_approval_stage', this.application.id), {
                assigned_to_id: this.assigned_to_id,
                id: this.selectedStage.id,
            }).then(response => {
                this.processing = false
                this.showAssignModal = false
                this.$inertia.reload()
                this.$swal({
                    icon: 'success',
                    text: 'Successfully updated',
                    showCancelButton: false,
                    timer: 3000
                })
            }).catch(error => {
                this.processing = false
                this.$swal({
                    icon: 'error',
                    text: 'An error occurred, please try again',
                    showCancelButton: false,
                    timer: 4000
                })
            })
        },
        acknowledge(id) {
            this.$swal({
                icon: 'warning',
                text: 'Are you sure?',
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No, cancel!",
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put(this.route('loans.applications.acknowledge_approval_stage', this.application.id), {
                        id: id,
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
                            text: 'An error occurred, please try again',
                            showCancelButton: false,
                            timer: 4000
                        })
                    })
                }
            });
        }
    },
}
</script>

<style scoped>
@media print {
    body {
        visibility: hidden; /* Hide entire body */
        margin: 0; /* Remove margins */
    }

    #print_btn {
        visibility: hidden; /* Hide the print button */
    }

    .main-div {
        visibility: visible; /* Make main content visible */
        position: absolute; /* Positioning to fit on page */
        left: 0; /* Align to left */
        top: 0; /* Align to top */
        margin: 0; /* Ensure no margins */
        padding: 0; /* Ensure no padding */
        width: 100%; /* Full width */
    }

    .child-div {
        display: inline-block; /* Maintain inline-block layout */
        width: 30%; /* Set width of each child */
        margin: 0; /* No margins */
        padding: 0; /* No padding */
        box-sizing: border-box; /* Include padding and borders in width */
    }

    /* Optional: Adjust text size for print if needed */
    body * {
        visibility: hidden; /* Hide all elements */
    }

    .main-div, .main-div * {
        visibility: visible; /* Show main content and its children */
    }

    /* Additional print-specific styling */
    .main-div {
        page-break-inside: avoid; /* Avoid page breaks inside this div */
    }

    /* Ensure child divs stack neatly */
    .child-div {
        width: auto; /* Allow child divs to fit content */
        text-align: left; /* Align text to the left for better print readability */
    }
}

/* General styles */
.main-div {
    width: 100%; /* Full width of parent */
}

.child-div {
    display: inline-block; /* Use inline-block for horizontal layout */
    width: 30%; /* Set width of each child div */
    margin: 0; /* No extra space */
    text-align: center; /* Center align text */
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .child-div {
        width: 100%; /* Stack divs on smaller screens */
        margin: 10px 0; /* Vertical space for stacked divs */
    }
}

</style>
