<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.applications.index')">
                    Loans Applications
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Application #{{ application.id }}
            </h2>
        </template>
        <div class=" mx-auto">
            <loan-application-menu :application="application" :payment-types="paymentTypes"></loan-application-menu>
            <div class="bg-white rounded shadow p-6">

                <ol class="relative border-s border-gray-200">
                    <li class="mb-10 ms-6" v-for="(item,index) in application.stages">
                    <span
                        :class="[item.status==='pending'?'bg-yellow-400':'',item.status==='approved'?'bg-green-400':'',item.status==='rejected'?'bg-red-400':'',item.status==='sent_back'?'bg-orange-400':'',]"
                        class="absolute flex items-center justify-center w-6 h-6 bg-yellow-100 rounded-full -start-3 ring-8 ring-white ">
                        <font-awesome-icon
                            :class="[item.status==='pending'?'text-yellow-800':'',item.status==='approved'?'text-green-800':'',item.status==='rejected'?'text-red-800':'',item.status==='sent_back'?'text-orange-800':'',]"
                            class="w-2.5 h-2.5 " icon="check-circle"/>
                    </span>
                        <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 ">
                            {{ item.name }}
                            <span
                                :class="[item.status==='pending'?'bg-yellow-400':'',item.status==='approved'?'bg-green-400':'',item.status==='rejected'?'bg-red-400':'',item.status==='sent_back'?'bg-orange-400':'',]"
                                class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded  ms-3">
                                <span v-if="item.status==='pending'">pending</span>
                                <span v-if="item.status==='approved'">approved</span>
                                <span v-if="item.status==='rejected'">rejected</span>
                            </span>
                        </h3>
                        <p class="mb-4  font-normal text-gray-500 text-xs " v-if="item.stage && item.stage.description">
                            {{ item.stage.description }}
                        </p>
                        <p v-if="item.assigned_to" class="block mb-2 text-sm font-normal leading-none text-gray-800 ">
                            Assigned to:
                            <inertia-link :href="route('users.show', item.assigned_to.id)"
                                          tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                {{ item.assigned_to.name }}
                            </inertia-link>
                        </p>
                        <time class="block mb-2 text-sm font-normal leading-none text-gray-400" v-if="item.received_at">
                            Received at {{ $filters.time(item.received_at) }}
                        </time>
                        <time class="block mb-2 text-sm font-normal leading-none text-gray-400"
                              v-if="item.acknowledged_at">
                            Acknowledged at {{ $filters.time(item.acknowledged_at) }}
                        </time>
                        <time class="block mb-2 text-sm font-normal leading-none text-gray-400"
                              v-if="item.completed_at">
                            Completed at {{ $filters.time(item.completed_at) }}
                        </time>

                        <p v-if="item.approval_notes" class="mt-2">{{ item.approval_notes }}</p>
                        <div class="mt-4">
                            <jet-primary-button
                                v-if="can('loans.applications.approval_stages.assign') && item.status==='pending'"
                                @click.native="selectedStage=item;showAssignModal = true;" class="mr-2">
                                Assign
                            </jet-primary-button>
                            <jet-primary-button
                                v-if="item.assigned_to_id===$page.props.auth.user.id && item.status==='pending' && !item.acknowledged"
                                @click.native="acknowledge(item.id)" class="mr-2">
                                Acknowledge
                            </jet-primary-button>
                            <jet-success-button
                                v-if="item.id===application.current_loan_application_linked_approval_stage_id && (can('loans.applications.approval_stages.change_status_for_all') && (item.status==='pending'||item.status==='sent_back')||(item.assigned_to_id===$page.props.auth.user.id && (item.status==='pending'||item.status==='sent_back'))) "
                                @click.native="selectedStage=item;stage_status=item.status;showChangeStatusModal = true">
                                Change Status
                            </jet-success-button>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
        <jet-dialog-modal :show="showAssignModal" @close="showAssignModal = false">
            <template #title>
                Assign Approver
            </template>

            <template #content>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <jet-label for="assigned_to_id" value="Approver"/>
                        <Multiselect
                            v-model="assigned_to_id"
                            v-bind="usersMultiSelect"
                        />
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="showAssignModal = false">
                    Cancel
                </jet-secondary-button>

                <jet-success-button class="ml-2" @click.native="assignApprover"
                                    :class="{ 'opacity-25': processing }"
                                    :disabled="processing">
                    Save
                </jet-success-button>
            </template>
        </jet-dialog-modal>
        <jet-dialog-modal :show="showChangeStatusModal" @close="showChangeStatusModal = false">
            <template #title>
                Change Status for stage: {{ selectedStage.name }}
            </template>
            <template #content>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <jet-label for="stage_status" value="Status"/>
                        <select-input
                            v-model="stage_status"
                            class="w-full"
                            name="stage_status">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="sent_back">Send Back</option>
                        </select-input>
                    </div>

                    <div>
                        <jet-label for="stage_status_comment" value="Notes"/>
                        <textarea-input id="stage_status_comment" class="mt-1 block w-full"
                                        v-model="stage_status_comment"/>

                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="showChangeStatusModal = false">
                    Cancel
                </jet-secondary-button>

                <jet-success-button class="ml-2" @click.native="changeStatus"
                                    :class="{ 'opacity-25': processing }"
                                    :disabled="processing">
                    Save
                </jet-success-button>
            </template>
        </jet-dialog-modal>
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
import LoanApplicationMenu from "./LoanApplicationMenu.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import JetDialogModal from '@/Jetstream/DialogModal.vue'

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
        TextareaInput, JetInputError, JetInput,
        JetCheckbox,
        JetDialogModal,
        FontAwesomeIcon,
        LoanApplicationMenu,
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
        application: Object,
        paymentTypes: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                status: this.application.status,
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

</style>
