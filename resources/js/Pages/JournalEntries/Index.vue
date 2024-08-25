<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Journal Entries
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="filter_chart_of_account_id" value="Account"/>
                        <Multiselect
                            id="filter_chart_of_account_id"
                            v-model="form.chart_of_account_id"
                            mode="single"
                            :searchable="true"
                            :options="$page.props.chartOfAccounts"
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
            <inertia-link v-if="can('accounting.journal_entries.create')" class="btn btn-blue"
                          :href="route('accounting.journal_entries.create')">
                <span>Create </span>
                <span class="hidden md:inline">Journal Entry</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Branch</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Transaction #</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Type</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Account</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Debit</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Credit</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="journalEntry in journalEntries.data" :key="journalEntry.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                {{ journalEntry.id }}
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="journalEntry.branch">
                                {{ journalEntry.branch.name }}
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                {{ journalEntry.date }}
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                {{ journalEntry.transaction_number }}
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="journalEntry.transaction_type==='expense'">
                                Expense
                            </span>
                            <span class="px-6 py-4 flex items-center"
                                  v-if="journalEntry.transaction_type==='invoice_payment'">
                                Invoice Payment
                            </span>
                            <span class="px-6 py-4 flex items-center"
                                  v-if="journalEntry.transaction_type==='manual_entry'">
                                Manual Entry
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="journalEntry.chart_of_account">
                                {{ journalEntry.chart_of_account.name }}
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center">
                                {{ $filters.currency(journalEntry.debit) }}
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center">
                                {{ $filters.currency(journalEntry.credit) }}
                            </span>
                        </td>

                    </tr>
                    <tr v-if="journalEntries.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="8">No Journal Entries found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="journalEntries.links"/>
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
import AppLayout from '@/Layouts/AppLayout.vue'
import Icon from '@/Jetstream/Icon.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import SearchFilter from '@/Jetstream/SearchFilter.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'

export default {
    components: {
        AppLayout,
        Icon,
        Pagination,
        SearchFilter,
        FilterSearch,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        journalEntries: Object,
        currencies: Object,
        chartOfAccounts: Object,
        filters: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                branch_id: this.filters.branch_id,
                currency_id: this.filters.currency_id,
                chart_of_account_id: this.filters.chart_of_account_id,
                start_date: this.filters.start_date,
                end_date: this.filters.end_date,
                date_range: this.filters.date_range,
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Journal Entries",
            pageDescription: "Manage Journal Entries",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('accounting.journal_entries.index', Object.keys(query).length ? query : {}))
            }, 500),
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

            this.$inertia.delete(this.route('accounting.journal_entries.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
