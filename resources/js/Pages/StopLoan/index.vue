<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Stop Order
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search class="w-80 max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="status" value="Status"/>
                        <select v-model="form.status"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <jet-label for="filter_member_id" value="Member"/>
                        <Multiselect
                            v-model="form.member_id"
                            mode="single"
                            :required="true"
                            v-bind="membersMultiSelect"
                        />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_current_stage_id" value="Current Stage"/>
                        <Multiselect
                            v-model="form.current_stage_id"
                            mode="single"
                            value-prop="id"
                            label="name"
                            :searchable="true"
                            :options="approvalStages"
                        />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_next_stage_id" value="Next Stage"/>
                        <Multiselect
                            v-model="form.next_stage_id"
                            mode="single"
                            value-prop="id"
                            label="name"
                            :searchable="true"
                            :options="approvalStages"
                        />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_date_range" value="Date Range"/>
                        <flat-pickr
                            v-model="form.date_range"
                            class="form-control w-full"
                            placeholder="Select date range"
                            :config="{mode:'range',dateFormat:'Y-m-d'}"
                            name="date_range">
                        </flat-pickr>
                    </div>
                </div>
            </filter-search>
            <inertia-link class="btn btn-blue" :href="route('loans.stop_loan.create')">
                <span>Create </span>
                <span class="hidden md:inline">Stop Order</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Member Account Holder</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Member Account No.</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Monthly Installment</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Member Bank</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Reference</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Funding BankName</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Stop Order Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="result in results.data" :key="result.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 <inertia-link :href="route('loans.applications.show', result.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.id }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="result.member_account_holder">
                                 <inertia-link :href="route('members.show', result.member_id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.member_account_holder }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            {{ result.member_account_number }}
                        </td>
                        <td class="border-t">
                           {{ result.monthly_installment }}
                        </td>
                        <td class="border-t">
                           {{ result.bank_name }}
                        </td>
                        <td class="border-t">
                           {{ result.reference }}
                        </td>

                        <td class="border-t">
                           {{ result.mp_bank_name }}
                        </td>

                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ result.stop_order_date }}
                            </span>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('loans.stop_loan.show', result.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/>
                                </inertia-link>
                                <inertia-link
                                              :href="route('loans.stop_loan.edit', result.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="Edit">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a href="#"
                                   @click="deleteAction(result.id)"
                                   class="text-red-600 hover:text-red-900" title="Delete">
                                    <font-awesome-icon icon="trash"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="results.data.length === 0">
                        <td class="border-t px-6 py-4 text-center" colspan="9">No records found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="results.links"/>
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

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
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
const fetchUsers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type_not_in=member&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + '(#' + item.id + ')'}
    })
}
const fetchMembers = async (query) => {
    let where = ''

    const response = await fetch(
        route('members.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + '(#' + item.id + ')'}
    })
}
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'

export default {
    components: {
        AppLayout,
        Pagination,
        FilterSearch,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        results: Object,
        filters: Object,
        currencies: Object,
        approvalStages: Object,
        purposes: Object,
        products: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                status: this.filters.status,
                currency_id: this.filters.currency_id,
                loan_product_id: this.filters.loan_product_id,
                loan_purpose_id: this.filters.loan_purpose_id,
                loan_officer_id: this.filters.loan_officer_id,
                member_id: this.filters.member_id,
                fund_id: this.filters.fund_id,
                loan_application_checklist_id: this.filters.loan_application_checklist_id,
                current_stage_id: this.filters.current_stage_id,
                next_stage_id: this.filters.next_stage_id,
                branch_id: this.filters.branch_id,
                date_range: this.filters.date_range,
                processing: false
            },
            usersMultiSelect: {
                placeholder: 'Search for Loan Officer',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query || this.filters.loan_officer_id)
                }
            },
            membersMultiSelect: {
                placeholder: 'Search for Member',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchMembers(query || this.filters.member_id)
                }
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Loan Applications",
            pageDescription: "Manage Loan Applications",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('loans.stop_loan.index', Object.keys(query).length ? query : {}))
            }, 1000),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {
            console.log(this.selectedRecord);
            this.$inertia.delete(this.route('loans.stop_loan.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
