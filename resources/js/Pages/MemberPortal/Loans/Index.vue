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
                        <jet-label for="filter_currency_id" value="Currency"/>
                        <Multiselect
                            id="filter_currency_id"
                            v-model="form.currency_id"
                            value-prop="id"
                            label="name"
                            mode="single"
                            :searchable="true"
                            :options="currencies"
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
            <inertia-link class="btn btn-blue" :href="route('portal.loans.applications.create')">
                <span>Apply for a Loan</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Loan Officer</th>
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
                                 <inertia-link :href="route('portal.loans.show', result.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.id }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center" v-if="result.loan_officer">
                                    {{ result.loan_officer.name }}
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
                                <inertia-link :href="route('portal.loans.show', result.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/> View
                                </inertia-link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="results.data.length === 0">
                        <td class="border-t px-6 py-4 text-center" colspan="8">No records found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="results.links"/>
        </div>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>

</template>

<script>


import AppLayout from '@/Pages/MemberPortal/Layouts/AppLayout.vue'
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
                this.$inertia.get(this.route('portal.loans.index', Object.keys(query).length ? query : {}))
            }, 1000),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
}
</script>

<style scoped>

</style>
