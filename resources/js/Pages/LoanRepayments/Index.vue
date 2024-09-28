<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Loan Repayments
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                </div>
            </filter-search>
            <div>
                <inertia-link v-if="can('loans.transactions.create')" class="btn btn-blue"
                          :href="route('loans.repayments.create_bulk_repayment')">
                <span>Add </span>
                <span class="hidden md:inline">Bulk Repayments</span>
            </inertia-link>
            <inertia-link v-if="can('loans.transactions.create')" class="btn btn-blue ml-1"
                          :href="route('loans.repayments.add')">
                <span>Add </span>
                <span class="hidden md:inline">Add Transaction</span>
            </inertia-link>
            </div>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Amount</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Client</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Loan #</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="result in results.data" :key="result.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            <inertia-link
                                :href="route('loans.transactions.show', result.id)"
                                tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                {{ result.id }}
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4">
                            <span>
                                {{ $filters.currency(result.amount) }}
                            </span>
                        </td>
                        <td class="border-t px-6 py-4">
                            <span>
                                {{ result.submitted_on }}
                            </span>
                        </td>
                        <td class="border-t px-6 py-4">
                            <inertia-link v-if="result.loan && result.loan.client"
                                          :href="route('clients.show', result.loan.client.id)"
                                          tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                {{ result.loan.client.name }}
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4">
                            <inertia-link v-if="result.loan "
                                          :href="route('loans.show', result.loan.id)"
                                          tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                {{ result.loan.id }}
                            </inertia-link>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center space-x-2">
                                <inertia-link
                                    :href="route('loans.transactions.show', result.id)"
                                    tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    <font-awesome-icon icon="eye"/>
                                </inertia-link>
                                <inertia-link class="text-indigo-600 hover:text-indigo-900"
                                    v-if="result.loan && result.loan.status==='active'  && !result.reversed && can('loans.transactions.update')"
                                    :href="route('loans.transactions.edit',result.id)">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a
                                   :href="route('loans.transactions.pdf',result.id)"
                                   class="text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                   target="_blank">
                                    <font-awesome-icon icon="file-pdf"/>
                                </a>
                                <a
                                   :href="route('loans.transactions.print',result.id)"
                                   class=" text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
                                   target="_blank">
                                    <font-awesome-icon icon="print"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="results.data.length === 0">
                        <td class="border-t px-6 py-4 text-center" colspan="2">No records found.</td>
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
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    components: {
        FontAwesomeIcon,
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

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Loan Repayments",
            pageDescription: "Manage  Loan Repayments",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('loans.repayments.index', Object.keys(query).length ? query : {}))
            }, 500),
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
