<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('reports.index')">Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('reports.savings.index')">
                    Savings Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Balances
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
                    <jet-label for="savings_product_id" value="Product"/>
                    <Multiselect
                        class="mt-1"
                        label="name"
                        value-prop="id"
                        id="savings_product_id"
                        v-model="form.savings_product_id"
                        :options="products"
                    />
                </div>
                <div class="mb-2">
                    <jet-label for="savings_officer_id" value="Savings Officer"/>
                    <Multiselect
                        class=""
                        label="name"
                        value-prop="id"
                        id="savings_officer_id"
                        v-model="form.savings_officer_id"
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
                    <a :href="route('reports.savings.balance',form)+'&download=1&download_type=pdf'"
                       class="text-red-400">
                        <font-awesome-icon icon="file-pdf"></font-awesome-icon>
                    </a>
                    <a :href="route('reports.savings.balance',form)+'&download=1&download_type=excel'"
                       class="ml-2 text-green-400">
                        <font-awesome-icon icon="file-excel"></font-awesome-icon>
                    </a>
                    <a :href="route('reports.savings.balance',form)+'&download=1&download_type=csv'"
                       class="ml-2 text-green-400">
                        <font-awesome-icon icon="file-csv"></font-awesome-icon>
                    </a>
                </div>
                <div class="p-4 mt-4">
                    <h3>By Branch</h3>
                    <div class="overflow-x">
                        <table class="w-full whitespace-no-wrap mt-4">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="p-2 font-medium text-gray-500">Branch</th>
                                <th class="p-2 font-medium text-gray-500">Deposits</th>
                                <th class="p-2 font-medium text-gray-500">Withdrawals</th>
                                <th class="p-2 font-medium text-gray-500">Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="result in results.by_branch" :key="result.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t p-2">
                                    <span>{{ result.branch }}</span>
                                </td>

                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.credit) }}</span>
                                </td>
                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.debit) }}</span>
                                </td>
                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.balance) }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-4 mt-4">
                    <h3>By Officer</h3>
                    <div class="overflow-x">
                        <table class="w-full whitespace-no-wrap mt-4">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="p-2 font-medium text-gray-500">Officer</th>
                                <th class="p-2 font-medium text-gray-500">Deposits</th>
                                <th class="p-2 font-medium text-gray-500">Withdrawals</th>
                                <th class="p-2 font-medium text-gray-500">Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="result in results.by_savings_officer" :key="result.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t p-2">
                                    <span>{{ result.savings_officer }}</span>
                                </td>

                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.credit) }}</span>
                                </td>
                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.debit) }}</span>
                                </td>
                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.balance) }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-4 mt-4">
                    <h3>By Date</h3>
                    <div class="overflow-x">
                        <table class="w-full whitespace-no-wrap mt-4">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="p-2 font-medium text-gray-500">Date</th>
                                <th class="p-2 font-medium text-gray-500">Deposits</th>
                                <th class="p-2 font-medium text-gray-500">Withdrawals</th>
                                <th class="p-2 font-medium text-gray-500">Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="result in results.by_date" :key="result.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t p-2">
                                    <span>{{ result.submitted_on }}</span>
                                </td>

                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.credit) }}</span>
                                </td>
                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.debit) }}</span>
                                </td>
                                <td class="border-t p-2">
                                    <span>{{ $filters.currency(result.balance) }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
        <teleport to="head">
            <title>Savings Balances</title>
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
                savings_officer_id: this.filters.savings_officer_id,
                gender: this.filters.gender,
                start_date: this.filters.start_date,
                end_date: this.filters.end_date,
                branch_id: this.filters.branch_id,
                savings_product_id: this.filters.savings_product_id,
                age_from: this.filters.age_from,
                age_to: this.filters.age_to,
                processing: false,
            },
            usersMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Savings Officer',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query || this.filters.savings_officer_id)
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
            this.$inertia.get(this.route('reports.savings.balance', Object.keys(query).length ? query : {}))
        },
        apply() {
            let query = pickBy(this.form)
            this.$inertia.get(this.route('reports.savings.balance', Object.keys(query).length ? query : {}))
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
