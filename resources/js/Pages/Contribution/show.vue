<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.applications.index')">
                    Contribution
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Contribution #{{ memberContribution.id }}
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow p-6">
                <div class="text-right">
                    <button class="btn btn-primary" type="button" @click="printDiv()" id="print_btn">Print</button>
                </div>
                <ol class="relative border-s border-gray-200">
                    <li class="mb-10 ms-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div>{{ memberContribution.member_account_holder }}</div>

                        </div>
                    </li>
                </ol>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                    <div>

                    </div>
                    <div id="printDiv">
                        <table class="w-full border-collapse border border-gray-200">
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Surname</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                    memberContribution.Surname }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Name</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                    memberContribution.name }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Member Category</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                    memberContribution.member_category }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Id no
                                </th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                    memberContribution.id_no }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Contri 15 per
                                </th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                    memberContribution.contri_15_per }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Contri 30 per</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                    memberContribution.contri_30_per }}</td>
                            </tr>
                            <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Total contribution</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500">{{
                    memberContribution.total_contribution
                                    }}</td>
                            </tr>
                            <!-- <tr class="text-left bg-slate-50">
                                <th class="border border-gray-200 p-2 font-medium text-gray-500">Signature</th>
                                <td class="border border-gray-200 p-2 font-medium text-gray-500"></td>
                            </tr> -->
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
        memberContribution: Object,
        paymentTypes: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                status: this.memberContribution.status,
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
</style>
