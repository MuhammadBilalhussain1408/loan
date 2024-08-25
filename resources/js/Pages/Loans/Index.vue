<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Loans
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-80 max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="status" value="Status"/>
                        <select v-model="form.status"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="active">Active</option>
                            <option value="closed">Closed</option>
                            <option value="withdrawn">Withdrawn</option>
                            <option value="written_off">Written Off</option>
                            <option value="rescheduled">Rescheduled</option>
                            <option value="overpaid">Overpaid</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_member_category_id" value="Category"/>
                        <Multiselect
                            id="filter_member_category_id"
                            v-model="form.member_category_id"
                            value-prop="id"
                            label="name"
                            mode="single"
                            :searchable="true"
                            :options="funds"
                        />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_loan_purpose_id" value="Loan Purpose"/>
                        <Multiselect
                            id="filter_loan_purpose_id"
                            v-model="form.loan_purpose_id"
                            value-prop="id"
                            label="name"
                            mode="single"
                            :searchable="true"
                            :options="purposes"
                        />
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
                        <jet-label for="filter_loan_officer_id" value="Loan Officer"/>
                        <Multiselect
                            v-model="form.loan_officer_id"
                            mode="single"
                            :required="true"
                            v-bind="usersMultiSelect"
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
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Loan Officer</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Member</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Product</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Amount</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Balance</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Disbursed</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="result in results.data" :key="result.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 <inertia-link :href="route('loans.show', result.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.id }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 <inertia-link :href="route('users.show', result.loan_officer_id)"
                                               v-if="result.loan_officer"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.loan_officer.name }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="result.member">
                                 <inertia-link :href="route('members.show', result.member_id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.member.name }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                           <span class="flex items-center" v-if="result.product">
                                {{ result.product.name }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ $filters.formatNumber(result.principal) }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ $filters.formatNumber(result.total_outstanding_derived) }}
                            </span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                           <span class="px-2 bg-orange-600 text-white rounded text-sm"
                                 v-if="result.status==='pending'||result.status==='submitted'">
                                pending approval
                            </span>
                            <span class="px-2 bg-yellow-600 text-white rounded text-sm"
                                  v-if="result.status==='approved'">
                                awaiting disbursement
                            </span>
                            <span class="px-2 bg-blue-600 text-white rounded text-sm"
                                  v-if="result.status==='active'">
                                active
                            </span>
                            <span class="px-2 bg-red-600 text-white rounded text-sm"
                                  v-if="result.status==='rejected'">
                                rejected
                            </span>
                            <span class="px-2 bg-red-600 text-white rounded text-sm"
                                  v-if="result.status==='withdrawn'">
                               withdrawn
                            </span>
                            <span class="px-2 bg-red-600 text-white rounded text-sm"
                                  v-if="result.status==='written_off'">
                                written off
                            </span>
                            <span class="px-2 bg-green-600 text-white rounded text-sm"
                                  v-if="result.status==='closed'">
                                closed
                            </span>
                            <span class="px-2 bg-orange-600 text-white rounded text-sm"
                                  v-if="result.status==='pending_reschedule'">
                                pending reschedule
                            </span>
                            <span class="px-2 bg-blue-600 text-white rounded text-sm"
                                  v-if="result.status==='rescheduled'">
                                rescheduled
                            </span>
                            <span class="px-2 bg-orange-600 text-white rounded text-sm"
                                  v-if="result.status==='overpaid'">
                                overpaid
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ result.disbursed_on_date }}
                            </span>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('loans.show', result.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/>
                                </inertia-link>
                                <inertia-link v-if="can('loans.update') && result.status==='submitted'"
                                              :href="route('loans.edit', result.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a href="#" v-if="can('loans.destroy') && result.status==='submitted'"
                                   @click="deleteAction(result.id)"
                                   class="text-red-600 hover:text-red-900" title="Delete">
                                    <font-awesome-icon icon="trash"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="results.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="11">No records found.</td>
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
        funds: Object,
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
                loan_provisioning_id: this.filters.loan_provisioning_id,
                loan_officer_id: this.filters.loan_officer_id,
                member_id: this.filters.member_id,
                fund_id: this.filters.fund_id,
                loan_purpose_id: this.filters.loan_purpose_id,
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
            pageTitle: "Loans",
            pageDescription: "Manage Loans",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('loans.index', Object.keys(query).length ? query : {}))
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

            this.$inertia.delete(this.route('loans.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
