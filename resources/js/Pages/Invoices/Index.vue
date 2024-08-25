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
                            <option value="paid">Paid</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <jet-label for="sponsor" value="Sponsor"/>
                        <select v-model="form.sponsor"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="cash">Cash</option>
                            <option value="co_payer">CoPayer</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_copayer" value="CoPayer"/>
                        <Multiselect
                            id="filter_copayer"
                            v-model="form.co_payer_id"
                            mode="single"
                            :searchable="true"
                            :options="$page.props.coPayers"
                        />
                    </div>

                    <div class="mb-2">
                        <jet-label for="filter_patient_id" value="Patient"/>
                        <Multiselect
                            v-model="form.patient_id"
                            mode="single"
                            :required="true"
                            v-bind="patientsMultiSelect"
                        />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_doctor_id" value="Doctor"/>
                        <Multiselect
                            v-model="form.doctor_id"
                            mode="single"
                            :required="true"
                            v-bind="doctorsMultiSelect"
                        />
                    </div>
                    <div class="mb-2">
                        <jet-label for="filter_currency_id" value="Currency"/>
                        <Multiselect
                            id="filter_currency_id"
                            v-model="form.currency_id"
                            mode="single"
                            :searchable="true"
                            :options="$page.props.currencies"
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
            <inertia-link class="btn btn-blue" :href="route('billing.invoices.create')">
                <span>Create </span>
                <span class="hidden md:inline">Invoice</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Reference</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Patient</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Doctor</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Currency</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Patient</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">CoPayer</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Balance</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="invoice in invoices.data" :key="invoice.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 <inertia-link :href="route('billing.invoices.show', invoice.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.id }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 <inertia-link :href="route('billing.invoices.show', invoice.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.reference }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="invoice.patient">
                                 <inertia-link :href="route('patients.show', invoice.patient_id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.patient.name }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="invoice.doctor">
                                 <inertia-link :href="route('users.show', invoice.doctor_id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ invoice.doctor.name }}
                                </inertia-link>
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center" v-if="invoice.currency">
                                {{ invoice.currency.code }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ $filters.formatNumber(invoice.cash_amount) }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ $filters.formatNumber(invoice.co_payer_amount) }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ $filters.formatNumber(invoice.balance) }}
                            </span>
                        </td>
                        <td class="border-t">
                           <span class="px-6 py-4 flex items-center">
                                {{ invoice.date }}
                            </span>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('billing.invoices.show', invoice.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/>
                                </inertia-link>
                                <inertia-link v-if="can('billing.invoices.update')"
                                              :href="route('billing.invoices.edit', invoice.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a href="#" v-if="can('billing.invoices.destroy')" @click="deleteAction(invoice.id)"
                                   class="text-red-600 hover:text-red-900" title="Delete">
                                    <font-awesome-icon icon="trash"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="invoices.data.length === 0">
                        <td class="border-t px-6 py-4" colspan="9">No invoices found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="invoices.links"/>
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
const fetchDoctors = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type=doctor&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + '(' + item.practice_number + ')'}
    })
}
const fetchPatients = async (query) => {
    let where = ''

    const response = await fetch(
        route('patients.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + '(' + item.id + ')'}
    })
}
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
        invoices: Object,
        filters: Object,
        currencies: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                status: this.filters.status,
                currency_id: this.filters.currency_id,
                co_payer_id: this.filters.co_payer_id,
                sponsor: this.filters.sponsor,
                doctor_id: this.filters.doctor_id,
                patient_id: this.filters.patient_id,
                date_range: this.filters.date_range,
                processing: false
            },
            doctorsMultiSelect: {
                placeholder: 'Search for Doctor',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchDoctors(query || this.filters.doctor_id)
                }
            },
            patientsMultiSelect: {
                placeholder: 'Search for Patient',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchPatients(query || this.filters.patient_id)
                }
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Billing",
            pageDescription: "Manage Billing",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('billing.invoices.index', Object.keys(query).length ? query : {}))
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

            this.$inertia.delete(this.route('billing.invoices.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
