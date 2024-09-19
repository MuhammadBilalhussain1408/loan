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
        <div class=" mx-auto">
            <div class="bg-white rounded shadow p-6">
                <div class="text-right">
                    <button class="btn btn-primary" type="button" @click="printDiv()" id="print_btn">Print</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                    <div>
                        <img :src="this.assetUrl + 'images/mopado-high-resolution-logo-1.png'"
                            style="height: 200px; margin:0; padding: 0;" alt="">
                    </div>
                    <div>
                        <h2>Member of Parliment And Desiginated Office Bearers Pension Fund</h2>
                        <table>
                            <tr>
                                <td class="p-2 font-medium text-gray-500">
                                    4<sup>th</sup> Floor, Liqagha House
                                </td>
                                <td class="p-2 font-medium text-gray-500">
                                    P.O. Box 4639
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 font-medium text-gray-500">
                                    Office No. 401, Manzini
                                </td>
                                <td class="p-2 font-medium text-gray-500">
                                    Manzini Swaziland
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 font-medium text-gray-500">
                                    Nkoseluhlaza Street
                                </td>
                                <td class="p-2 font-medium text-gray-500">
                                    Tel:(00268) 2505 7033/4
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 font-medium text-gray-500">

                                </td>
                                <td class="p-2 font-medium text-gray-500">
                                    Fax:(00268) 2505 7034
                                </td>
                            </tr>
                        </table>
                        <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div>
                                <p>4<sup>th</sup> Floor, Liqagha House</p>
                                <p>Office No. 401, Manzini</p>
                                <p>Nkoseluhlaza Street</p>
                            </div>
                            <div>
                                <p>P.O. Box 4639</p>
                                <p>Manzini Swaziland</p>
                                <p>Tel:(00268) 2505 7033/4</p>
                                <p>Fax:(00268) 2505 7034</p>
                            </div>
                        </div> -->
                    </div>
                </div>
                <hr />
                <ol class="relative border-s border-gray-200">
                    <li class="mb-10 ms-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div>{{ stopLoan.member_account_holder }}</div>

                        </div>
                    </li>
                </ol>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                    <div>

                    </div>
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
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Member Account Number
                                </th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                                    stopLoan.member_account_holder }}</td>
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
                    </div>
                </div>
                <p class="mt-5">I understand that this stop order instructions shall remain in force until advised
                    otherwise by
                    MOPADO only.</p>
                <div class="main-div" style="">
                    <div class="child-div">_____________________<br>Account Holder</div>
                    <div class="child-div">_____________________<br>MOPADO</div>
                    <div class="child-div">
                        <div style="border: 1px solid black; height: 60px; width: 100px; text-align: center; margin: 0;">Stamp
                        </div>
                    </div>
                </div>
                <div class="main-div">
                    <div class="child-div"></div>
                    <div class="child-div">
                        <div style="border: 1px solid black; height: 100px; width: 150px; text-align: center;">BANK
                            STAMP</div>
                    </div>
                    <div class="child-div"></div>
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
        visibility: hidden;
    }

    #print_btn {
        visibility: hidden;
    }

    /* #printDiv {
    visibility: visible;
    position: absolute;
    left: 0;
    top: 0;
  } */
}

.main-div {
    width: 100%;
    /* Full width of parent */
    padding: 10px;
    /* border: 1px solid #000; */
    /* Optional: border for visualization */
    box-sizing: border-box;
    /* Include padding in width calculation */
}

.child-div {
    display: inline-block;
    /* Use inline-block for horizontal layout */
    width: 30%;
    /* Set width of each child div */
    margin: 0 1%;
    /* Space between divs */
    padding: 20px;
    /* background-color: #f0f0f0; */
    /* Light gray background */
    text-align: center;
    box-sizing: border-box;
    /* Include padding in width calculation */
}

@media (max-width: 600px) {
    .child-div {
        width: 100%;
        /* Stack divs on smaller screens */
        margin: 10px 0;
        /* Vertical space for stacked divs */
    }
}
</style>
