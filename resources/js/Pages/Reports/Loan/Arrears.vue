<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('reports.index')">Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('reports.loans.index')">Loan
                    Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Arrears
            </h2>
        </template>
        <div class="mx-auto bg-white p-2 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                <div class="mb-2">
                    <jet-label for="branch_id" value="Branch"/>
                    <Multiselect
                        class="mt-1"
                        label="name"
                        value-prop="id"
                        id="branch_id"
                        v-model="form.branch_id"
                        :options="branches"
                    />
                </div>
                <div class="mb-2">
                    <jet-label for="loan_product_id" value="Product"/>
                    <Multiselect
                        class="mt-1"
                        label="name"
                        value-prop="id"
                        id="loan_product_id"
                        v-model="form.loan_product_id"
                        :options="products"
                    />
                </div>
                <div class="mb-2">
                    <jet-label for="loan_officer_id" value="Loan Officer"/>
                    <Multiselect
                        class=""
                        label="name"
                        value-prop="id"
                        id="loan_officer_id"
                        v-model="form.loan_officer_id"
                        v-bind="usersMultiSelect"
                    />
                </div>
                <div class="mb-2">
                    <jet-label for="filter_start_date" value="Start Date"/>
                    <flat-pickr
                        v-model="form.start_date"
                        class="form-control w-full"
                        placeholder="Select date"
                        :config="{}"
                        name="start_date">
                    </flat-pickr>
                </div>
                <div class="mb-2">
                    <jet-label for="filter_end_date" value="End Date"/>
                    <flat-pickr
                        v-model="form.end_date"
                        class="form-control w-full"
                        placeholder="Select date"
                        :config="{}"
                        name="end_date">
                    </flat-pickr>
                </div>
                <div class="mb-2">
                    <jet-label for="filter_gender" value="Gender"/>
                    <select v-model="form.gender" id="filter_gender"
                            class=" w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                        <option :value="null"/>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="mb-2">
                    <jet-label for="filter_age_from" value="Age From"/>
                    <jet-input id="filter_age_from" type="number"
                               v-model="form.age_from"
                               class="w-full">
                    </jet-input>
                </div>
                <div class="mb-2">
                    <jet-label for="filter_age_to" value="Age To"/>
                    <jet-input id="filter_age_to" type="number"
                               v-model="form.age_to"
                               class="w-full">
                    </jet-input>
                </div>
            </div>
            <div class="mb-2 flex items-end">
                <jet-button class="" @click="apply" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                    Search
                </jet-button>
                <jet-button class="ml-4" @click="reset">
                    Reset
                </jet-button>
            </div>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow">
                <div class="bg-gray-50 px-6 py-2 flex border-b">
                    <a :href="route('reports.loans.arrears',form)+'&download=1&download_type=pdf'"
                       class="text-red-400">
                        <font-awesome-icon icon="file-pdf"></font-awesome-icon>
                    </a>
                    <a :href="route('reports.loans.arrears',form)+'&download=1&download_type=excel'"
                       class="ml-2 text-green-400">
                        <font-awesome-icon icon="file-excel"></font-awesome-icon>
                    </a>
                    <a :href="route('reports.loans.arrears',form)+'&download=1&download_type=csv'"
                       class="ml-2 text-green-400">
                        <font-awesome-icon icon="file-csv"></font-awesome-icon>
                    </a>
                </div>
                <div class="overflow-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead class="bg-gray-50">
                        <tr class="text-left font-bold">

                            <th class="p-2 font-medium text-gray-500">Loan Officer</th>
                            <th class="p-2 font-medium text-gray-500">Branch</th>
                            <th class="p-2 font-medium text-gray-500">Client</th>
                            <th class="p-2 font-medium text-gray-500">Mobile</th>
                            <th class="p-2 font-medium text-gray-500">Loan#</th>
                            <th class="p-2 font-medium text-gray-500">Product</th>
                            <th class="p-2 font-medium text-gray-500">Disbursed Date</th>
                            <th class="p-2 font-medium text-gray-500">Maturity Date</th>
                            <th class="p-2 font-medium text-gray-500">Remaining</th>
                            <th class="p-2 font-medium text-gray-500">Amount</th>
                            <th class="p-2 font-medium text-gray-500">Principal</th>
                            <th class="p-2 font-medium text-gray-500">Interest</th>
                            <th class="p-2 font-medium text-gray-500">Fees</th>
                            <th class="p-2 font-medium text-gray-500">Penalties</th>
                            <th class="p-2 font-medium text-gray-500">Total</th>
                            <th class="p-2 font-medium text-gray-500">Outstanding</th>
                            <th class="p-2 font-medium text-gray-500">%</th>
                            <th class="p-2 font-medium text-gray-500">Days In Arrears</th>
                            <th class="p-2 font-medium text-gray-500">Days Since Last Payment</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="result in results.data" :key="result.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t p-2">
                                <inertia-link v-if=" result.loan_officer"
                                              :href="route('users.show', result.loan_officer.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.loan_officer.name }}
                                </inertia-link>
                            </td>
                            <td class="border-t p-2">
                             <span v-if="result.branch">
                                {{ result.branch.name }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                                <inertia-link v-if="result.client"
                                              :href="route('clients.show', result.client.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.client.name }}
                                </inertia-link>
                            </td>
                            <td class="border-t p-2">
                                <inertia-link v-if="result.client"
                                              :href="route('clients.show', result.client.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.client.mobile }}
                                </inertia-link>
                            </td>
                            <td class="border-t p-2">
                                <inertia-link
                                    :href="route('loans.show', result.id)"
                                    tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.id }}
                                </inertia-link>
                            </td>

                            <td class="border-t p-2" v-if="result.product">
                             <span>
                                {{ result.product.name }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ result.disbursed_on_date}}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ result.expected_maturity_date}}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ result.remaining_days}}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ $filters.currency(result.principal) }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ $filters.currency(result.principal_overdue) }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ $filters.currency(result.interest_overdue) }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ $filters.currency(result.fees_overdue) }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ $filters.currency(result.penalties_overdue) }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ $filters.currency(result.arrears_amount) }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ $filters.currency(result.total_outstanding_derived) }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ $filters.currency(result.percentage_overdue) }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ result.arrears_days }}
                            </span>
                            </td>
                            <td class="border-t p-2">
                             <span>
                                {{ result.days_since_last_payment }}
                            </span>
                            </td>
                        </tr>
                        <tr v-if="results.data.length === 0">
                            <td class="border-t px-6 py-4 text-center" colspan="11">No results found.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="mt-2">
                <pagination :links="results.links"/>
            </div>
        </div>
        <teleport to="head">
            <title>Arrears</title>
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
import Button from "@/Jetstream/Button.vue";
import mapValues from "lodash/mapValues";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink.vue";
import pickBy from "lodash/pickBy";
import Pagination from '@/Jetstream/Pagination.vue'


const fetchUsers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type_not_in=client&s=' + query,
        {}
    );
    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        item.name = item.name + ' (#' + item.id + ')';
        return item;
    })
}

export default {
    props: {
        report: Object,
        results: Object,
        filters: Object,
        products: Object,
        branches: Object,
    },
    components: {
        Button,
        SelectInput,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,
        JetDropdown,
        JetDropdownLink,
        JetNavLink,
        JetResponsiveNavLink,
        Pagination,
    },
    data() {
        return {
            form: {
                loan_officer_id: this.filters.loan_officer_id,
                gender: this.filters.gender,
                start_date: this.filters.start_date,
                end_date: this.filters.end_date,
                branch_id: this.filters.branch_id,
                loan_product_id: this.filters.loan_product_id,
                age_from: this.filters.age_from,
                age_to: this.filters.age_to,
                processing: false,
            },
            usersMultiSelect: {
                value: null,
                remark: null,
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
        }

    },
    mounted() {

    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
            let query = pickBy(this.form)
            this.$inertia.get(this.route(this.report.route, Object.keys(query).length ? query : {}))
        },
        apply() {
            let query = pickBy(this.form)
            this.$inertia.get(this.route('reports.loans.repayment', Object.keys(query).length ? query : {}))
        },
        download(event, type) {
            this.form.download = true;
            this.form.download_type = type;
        }
    },
    watch: {}
}
</script>
<style scoped>

</style>
