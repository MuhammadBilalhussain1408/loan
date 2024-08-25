<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('communication.campaigns.index')">
                    Campaigns
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Create
            </h2>
        </template>

        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="">
                            <jet-label for="name" value="Name"/>
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                       required autofocus/>
                            <jet-input-error :message="form.errors.name" class="mt-2" required/>
                        </div>
                        <div>
                            <jet-label for="campaign_type" value="Campaign Type"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="campaign_type" v-model="form.campaign_type" id="campaign_type">
                                <option value="sms">SMS</option>
                                <option value="email">Email</option>
                            </select>
                            <jet-input-error :message="form.errors.campaign_type" class="mt-2"/>
                        </div>
                        <div v-if="form.campaign_type==='sms'">
                            <jet-label for="sms_gateway_id" value="SMS Gateway"/>
                            <Multiselect
                                v-model="form.sms_gateway_id"
                                mode="single"
                                value-prop="id"
                                label="name"
                                :required="true"
                                :options="smsGateways"
                            />
                            <jet-input-error :message="form.errors.sms_gateway_id" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="trigger_type" value="Trigger Type"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="trigger_type" v-model="form.trigger_type" id="trigger_type">
                                <option value="direct">Direct</option>
                                <option value="schedule">Schedule</option>
                                <option value="triggered">Triggered</option>
                            </select>
                            <jet-input-error :message="form.errors.trigger_type" class="mt-2"/>
                        </div>
                        <div v-if="form.trigger_type==='schedule'">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                                <div>
                                    <jet-label for="scheduled_date" value="Schedule Date"/>
                                    <flat-pickr
                                        v-model="form.scheduled_date"
                                        class="form-control w-full"
                                        placeholder="Select date"
                                        name="date">
                                    </flat-pickr>
                                    <jet-input-error :message="form.errors.scheduled_date" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="scheduled_time" value="Schedule Time"/>
                                    <flat-pickr
                                        v-model="form.scheduled_time"
                                        :config="{time_24hr:true,noCalendar:true,enableTime:true,dateFormat:'H:i'}"
                                        class="form-control w-full"
                                        placeholder="Select date"
                                        name="date">
                                    </flat-pickr>
                                    <jet-input-error :message="form.errors.scheduled_time" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="schedule_frequency" value="Frequency"/>
                                    <jet-input id="schedule_frequency" type="text" class=" block w-full"
                                               v-model="form.schedule_frequency"/>
                                    <jet-input-error :message="form.errors.schedule_frequency" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="schedule_frequency_type" value="Frequency Type"/>
                                    <select
                                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                        name="schedule_frequency_type"
                                        v-model="form.schedule_frequency_type" id="schedule_frequency_type">
                                        <option value="days">Days</option>
                                        <option value="weeks">Weeks</option>
                                        <option value="months">Months</option>
                                        <option value="years">Years</option>
                                        <option value="selected_days">Selected Days</option>
                                    </select>
                                </div>
                                <div v-if="form.schedule_frequency_type==='selected_days'">
                                    <jet-label for="selected_days" value="Frequency"/>
                                    <Multiselect
                                        v-model="form.selected_days"
                                        mode="tags"
                                        :required="true"
                                        :options="['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']"
                                    />
                                    <jet-input-error :message="form.errors.selected_days" class="mt-2"/>
                                </div>
                            </div>
                        </div>
                        <div>
                            <jet-label for="communication_campaign_business_rule_id" value="Business Rule"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="account_type" v-model="form.communication_campaign_business_rule_id"
                                id="communication_campaign_business_rule_id">
                                <option v-for="item in availableBusinessRules" :value="item.id">
                                    {{ item.name }}
                                </option>
                            </select>
                            <jet-input-error :message="form.errors.communication_campaign_business_rule_id"
                                             class="mt-2"/>
                        </div>
                        <div class="mt-2" id="business_rule_msg" v-if="business_rule_description">
                            <div class=" flex items-center justify-between bg-green-500 rounded w-full">
                                <div class="p-4 text-white text-sm font-medium">{{
                                        business_rule_description
                                    }}
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="business_rule_name==='Active Clients'||business_rule_name==='Prospective Clients'||business_rule_name==='Active Loan Clients'||business_rule_name==='Loans in arrears'||business_rule_name==='Loans disbursed to clients'||business_rule_name==='Loan payments due'||business_rule_name==='Dormant Prospects'||business_rule_name==='Loan Payments Due (Overdue Loans)'||business_rule_name==='Loan Payments Received (Active Loans)'||business_rule_name==='Loan Payments Received (Overdue Loans)'||business_rule_name==='Happy Birthday'||business_rule_name==='Loan Fully Repaid'||business_rule_name==='Loans Outstanding after final instalment date'||business_rule_name==='Past Loan Clients'||business_rule_name==='Loan Submitted'||business_rule_name==='Loan Approved'||business_rule_name==='Loan Rejected'||business_rule_name==='Loan Disbursed'||business_rule_name==='Loan Rescheduled'||business_rule_name==='Loan Closed'||business_rule_name==='Loan Repayment'||business_rule_name==='Savings Submitted'||business_rule_name==='Savings Rejected'||business_rule_name==='Savings Approved'||business_rule_name==='Savings Activated'||business_rule_name==='Savings Dormant'||business_rule_name==='Savings Inactive'||business_rule_name==='Savings Closed'||business_rule_name==='Savings Deposit'||business_rule_name==='Savings Withdrawal'">
                            <jet-label for="branch_id" value="Branch"/>
                            <Multiselect
                                value-prop="id"
                                label="name"
                                id="branch_id"
                                v-model="form.branch_id"
                                mode="single"
                                :searchable="true"
                                :options="branches"
                            />
                            <jet-input-error :message="form.errors.branch_id" class="mt-2"/>
                        </div>
                        <div
                            v-if="business_rule_name==='Active Clients'||business_rule_name==='Prospective Clients'||business_rule_name==='Active Loan Clients'||business_rule_name==='Loans in arrears'||business_rule_name==='Loans disbursed to clients'||business_rule_name==='Loan payments due'||business_rule_name==='Dormant Prospects'||business_rule_name==='Loan Payments Due (Overdue Loans)'||business_rule_name==='Loan Payments Received (Active Loans)'||business_rule_name==='Loan Payments Received (Overdue Loans)'||business_rule_name==='Happy Birthday'||business_rule_name==='Loan Fully Repaid'||business_rule_name==='Loans Outstanding after final instalment date'||business_rule_name==='Past Loan Clients'||business_rule_name==='Loan Submitted'||business_rule_name==='Loan Approved'||business_rule_name==='Loan Rejected'||business_rule_name==='Loan Disbursed'||business_rule_name==='Loan Rescheduled'||business_rule_name==='Loan Closed'||business_rule_name==='Loan Repayment'">
                            <jet-label for="loan_officer_id" value="Loan Officer"/>
                            <Multiselect
                                value-prop="id"
                                label="name"
                                id="loan_officer_id"
                                v-model="form.loan_officer_id"
                                mode="single"
                                :searchable="true"
                                v-bind="loanOfficersMultiSelect"
                            />
                            <jet-input-error :message="form.errors.loan_officer_id" class="mt-2"/>
                        </div>
                        <div
                            v-if="business_rule_name==='Active Loan Clients'||business_rule_name==='Loans in arrears'||business_rule_name==='Loans disbursed to clients'||business_rule_name==='Loan payments due'||business_rule_name==='Dormant Prospects'||business_rule_name==='Loan Payments Due (Overdue Loans)'||business_rule_name==='Loan Payments Received (Active Loans)'||business_rule_name==='Loan Payments Received (Overdue Loans)'||business_rule_name==='Happy Birthday'||business_rule_name==='Loan Fully Repaid'||business_rule_name==='Loans Outstanding after final instalment date'||business_rule_name==='Past Loan Clients'||business_rule_name==='Loan Submitted'||business_rule_name==='Loan Approved'||business_rule_name==='Loan Rejected'||business_rule_name==='Loan Disbursed'||business_rule_name==='Loan Rescheduled'||business_rule_name==='Loan Closed'||business_rule_name==='Loan Repayment'">
                            <jet-label for="loan_product_id" value="Loan Product"/>
                            <Multiselect
                                value-prop="id"
                                label="name"
                                id="loan_product_id"
                                v-model="form.loan_product_id"
                                mode="single"
                                :searchable="true"
                                :options="loanProducts"
                            />
                            <jet-input-error :message="form.errors.loan_product_id" class="mt-2"/>
                        </div>
                        <div
                            v-if="business_rule_name==='Savings Submitted'||business_rule_name==='Savings Rejected'||business_rule_name==='Savings Approved'||business_rule_name==='Savings Activated'||business_rule_name==='Savings Dormant'||business_rule_name==='Savings Inactive'||business_rule_name==='Savings Closed'||business_rule_name==='Savings Deposit'||business_rule_name==='Savings Withdrawal'">
                            <jet-label for="savings_product_id" value="Savings Product"/>
                            <Multiselect
                                value-prop="id"
                                label="name"
                                id="savings_product_id"
                                v-model="form.savings_product_id"
                                mode="single"
                                :searchable="true"
                                :options="savingsProducts"
                            />
                            <jet-input-error :message="form.errors.savings_product_id" class="mt-2"/>
                        </div>
                        <div
                            v-if="business_rule_name==='Single Client'">
                            <jet-label for="client_id" value="Client"/>
                            <Multiselect
                                value-prop="id"
                                label="name"
                                id="client_id"
                                :required="true"
                                v-model="form.client_id"
                                mode="single"
                                :searchable="true"
                                v-bind="clientsMultiSelect"
                            />
                            <jet-input-error :message="form.errors.client_id" class="mt-2"/>
                        </div>
                        <div
                            v-if="business_rule_name==='Single User'">
                            <jet-label for="user_id" value="User"/>
                            <Multiselect
                                value-prop="id"
                                label="name"
                                id="user_id"
                                :required="true"
                                v-model="form.user_id"
                                mode="single"
                                :searchable="true"
                                v-bind="usersMultiSelect"
                            />
                            <jet-input-error :message="form.errors.user_id" class="mt-2"/>
                        </div>
                        <div
                            v-if="business_rule_name==='Loans in arrears'||business_rule_name==='Loans disbursed to clients'||business_rule_name==='Loan payments due'||business_rule_name==='Dormant Prospects'||business_rule_name==='Loan Payments Due (Overdue Loans)'||business_rule_name==='Loan Payments Received (Active Loans)'||business_rule_name==='Loan Payments Received (Overdue Loans)'||business_rule_name==='Loan Fully Repaid'||business_rule_name==='Loans Outstanding after final instalment date'||business_rule_name==='Past Loan Clients'"
                            class="grid grid-cols-2 gap-2">
                            <div>
                                <jet-label for="from_x" value="From X"/>
                                <jet-input id="from_x" type="text" class="mt-1 block w-full"
                                           v-model="form.from_x" required/>
                                <jet-input-error :message="form.errors.from_x" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="to_y" value="To Y"/>
                                <jet-input id="to_y" type="text" class="mt-1 block w-full"
                                           v-model="form.to_y" required/>
                                <jet-input-error :message="form.errors.to_y" class="mt-2"/>
                            </div>
                        </div>
                        <div
                            v-if="business_rule_name==='Loan Payments Due (Overdue Loans)'||business_rule_name==='Loan Payments Received (Overdue Loans)'"
                            class="grid grid-cols-2 gap-2">
                            <div>
                                <jet-label for="overdue_x" value="Overdue X"/>
                                <jet-input id="overdue_x" type="text" class="mt-1 block w-full"
                                           v-model="form.overdue_x" required/>
                                <jet-input-error :message="form.errors.overdue_x" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="overdue_y" value="Overdue Y"/>
                                <jet-input id="overdue_y" type="text" class="mt-1 block w-full"
                                           v-model="form.overdue_y" required/>
                                <jet-input-error :message="form.errors.overdue_y" class="mt-2"/>
                            </div>
                        </div>

                        <div>
                            <jet-label for="template_id" value="Choose Template"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="template_id" v-model="template_id" id="template_id">
                                <option></option>
                                <option v-for="item in selectedTemplates" :value="item.id">
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>
                        <div v-if="form.campaign_type==='email'">
                            <jet-label for="subject" value="Email Subject"/>
                            <jet-input id="subject" type="text" class="mt-1 block w-full"
                                       v-model="form.subject" required/>
                            <jet-input-error :message="form.errors.subject" class="mt-2"/>
                        </div>
                        <div v-if="form.campaign_type==='email'">
                            <jet-label for="description" value="Description"/>
                            <ckeditor :editor="editor"
                                      v-model="form.description"
                                      :config="editorConfig"></ckeditor>
                            <jet-input-error :message="form.errors.description" class="mt-2"/>
                        </div>
                        <div v-if="form.campaign_type==='sms'">
                            <jet-label for="description" value="Description"/>
                            <textarea-input v-if="form.campaign_type==='sms'" id="description"
                                            class="mt-1 block w-full"
                                            v-model="form.description"/>
                            <jet-input-error :message="form.errors.description" class="mt-2"/>
                        </div>
                        <div v-if="form.trigger_type==='schedule' || form.trigger_type==='triggered'">
                            <jet-label for="status" value="Status"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="status"
                                v-model="form.status" id="status">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="inactive">In-active</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">

                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                            Save
                        </jet-button>
                    </div>
                </form>
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
import ClassicEditor from '@tjmugova/ckeditor5-custom-build';
import UploadAdapter from "@/Jetstream/UploadAdaptor.vue";

const fetchUsers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return item
    })
}
const fetchLoanOfficers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return item
    })
}
const fetchClients = async (query) => {
    let where = ''
    const response = await fetch(
        route('clients.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return item
    })
}

export default {
    props: {
        communicationCampaignBusinessRules: Object,
        communicationCampaignAttachmentTypes: Object,
        templates: Object,
        smsGateways: Object,
        loanProducts: Object,
        branches: Object,
        savingsProducts: Object,
        client_id: String,
        user_id: String,
        campaign_type: String,
        sms_gateway_id: String,
    },
    components: {
        SelectInput,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,

    },
    data() {
        return {
            form: this.$inertia.form({
                sms_gateway_id: this.sms_gateway_id,
                communication_campaign_business_rule_id: null,
                communication_campaign_attachment_type_id: null,
                branch_id: null,
                savings_product_id: null,
                client_id: this.client_id,
                user_id: this.user_id,
                loan_officer_id: null,
                loan_product_id: null,
                subject: null,
                name: null,
                campaign_type: this.campaign_type,
                trigger_type: 'direct',
                scheduled_date: null,
                scheduled_time: null,
                schedule_frequency: null,
                schedule_frequency_type: null,
                selected_days: [],
                from_x: null,
                to_y: null,
                cycle_x: null,
                cycle_y: null,
                overdue_x: null,
                overdue_y: null,
                active: false,
                status: 'pending',
                description: ``,

            }),
            loanOfficersMultiSelect: {
                placeholder: 'Search for Loan Officer',
                valuePro: 'id',
                label: 'name',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchLoanOfficers(query || this.form.loan_officer_id)
                }
            },
            usersMultiSelect: {
                placeholder: 'Search for User',
                valuePro: 'id',
                label: 'name',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query || this.form.user_id)
                }
            },
            clientsMultiSelect: {
                valuePro: 'id',
                label: 'name',
                placeholder: 'Search for Client',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchClients(query || this.form.client_id)
                }
            },
            business_rule_name: null,
            business_rule_description: null,
            template_id: null,
            editorConfig: {
                table: {
                    toolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                },
                removePlugins: ['MediaEmbedToolbar', 'Title'],
                extraPlugins: [function (editor) {
                    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                        return new UploadAdapter(loader);
                    }
                }],
                language: 'nl',
            },
            editor: ClassicEditor,
            pageTitle: "Create Campaign",
            pageDescription: "Create Campaign",
        }

    },
    mounted() {
        if (this.client_id) {
            this.form.name = 'Single Message';
            this.form.communication_campaign_business_rule_id = 8;
        }
    },
    methods: {
        submit() {
            this.form.post(this.route('communication.campaigns.store'), {})

        },

    },
    computed: {
        selectedTemplates: function () {
            return this.templates.filter(item => {
                return item.type === this.form.campaign_type;
            })
        },
        availableBusinessRules:function () {
            if (this.form.trigger_type === 'triggered') {
                return this.communicationCampaignBusinessRules.filter(item => {
                    return item.is_trigger
                })
            } else {
                return this.communicationCampaignBusinessRules.filter(item => {
                    return !item.is_trigger
                })
            }
        }
    },
    watch: {
        'form.communication_campaign_business_rule_id': function (val) {
            this.communicationCampaignBusinessRules.forEach(item => {
                if (val === item.id) {
                    this.business_rule_description = item.description;
                    this.business_rule_name = item.name;
                }
            })
        },
        template_id: function (val) {
            this.selectedTemplates.forEach(item => {
                if (val === item.id) {
                    this.form.description = item.description;
                    if (item.type === 'email') {
                        this.form.subject = item.subject;
                    }

                }
            })
        }
    }
}
</script>
<style scoped>

</style>
