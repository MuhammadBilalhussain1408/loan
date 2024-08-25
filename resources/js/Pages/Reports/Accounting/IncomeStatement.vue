<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('reports.index')">Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('reports.accounting.index')">
                    Accounting Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Income Statement
            </h2>
        </template>
        <div class="mx-auto bg-white p-2 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="mb-2">
                    <jet-label for="filter_branch_id" value="Branch"/>
                    <Multiselect
                        :searchable="true"
                        v-model="form.branch_id"
                        mode="single"
                        value-prop="id"
                        label="name"
                        :options="branches"
                    />
                </div>
                <div class="mb-2">
                    <jet-label for="filter_currency_id" value="Currency"/>
                    <Multiselect
                        id="filter_currency_id"
                        v-model="form.currency_id"
                        mode="single"
                        :searchable="true"
                        value-prop="id"
                        label="name"
                        :options="currencies"
                    />
                </div>
                <div class="mb-2">
                    <jet-label for="filter_financial_period_id" value="Financial Period"/>
                    <Multiselect
                        id="filter_financial_period_id"
                        v-model="form.financial_period_id"
                        mode="single"
                        :searchable="true"
                        value-prop="id"
                        label="name"
                        :options="financialPeriods"
                    />
                </div>
                <div class="mb-2">
                    <jet-label for="filter_end_date" value="Start Date"/>
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
                    <a :href="route('reports.accounting.income_statement',form)+'&download=1&download_type=pdf'"
                       class="text-red-400">
                        <font-awesome-icon icon="file-pdf"></font-awesome-icon>
                    </a>
                    <a :href="route('reports.accounting.income_statement',form)+'&download=1&download_type=excel'"
                       class="ml-2 text-green-400">
                        <font-awesome-icon icon="file-excel"></font-awesome-icon>
                    </a>
                    <a :href="route('reports.accounting.income_statement',form)+'&download=1&download_type=csv'"
                       class="ml-2 text-green-400">
                        <font-awesome-icon icon="file-csv"></font-awesome-icon>
                    </a>
                </div>
                <div class="overflow-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                        <tr>
                            <th class="font-bold px-6 py-4 text-left">Account</th>
                            <th class="font-bold px-6 py-4 text-right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="">
                            <td class="border-t  text-gray-400 px-6 py-4" colspan="2">
                                Operating Income
                            </td>
                        </tr>
                        <tr v-for="(result,index) in results.income" :key="index"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t px-6 py-4 text-left">
                               <span class="">
                                        {{ result.name }}
                               </span>
                            </td>
                            <td class="border-t px-6 py-4  text-right">
                                <span class="">
                                    {{ $filters.formatNumber(result.balance) }}
                                </span>
                            </td>

                        </tr>
                        <tr>
                            <td class="border-t px-6 py-4 font-bold text-xl  text-left">
                                Total Operating Income
                            </td>
                            <td class="border-t px-6 py-4 font-bold text-xl text-right">
                                {{ $filters.formatNumber(totalOperatingIncome) }}
                            </td>
                        </tr>
                        <tr class="">
                            <td class="border-t  text-gray-400 px-6 py-4" colspan="2">
                                Cost of Goods Sold
                            </td>
                        </tr>
                        <tr v-for="(result,index) in results.expenses" :key="index"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t px-6 py-4 text-left">
                               <span class="">
                                        {{ result.name }}
                               </span>
                            </td>
                            <td class="border-t px-6 py-4  text-right">
                                <span class="">
                                    {{ $filters.formatNumber(result.balance) }}
                                </span>
                            </td>

                        </tr>
                        <tr>
                            <td class="border-t px-6 py-4 font-bold text-xl  text-left">
                                Total Cost of Goods Sold
                            </td>
                            <td class="border-t px-6 py-4 font-bold text-xl text-right">
                                {{ $filters.formatNumber(totalExpenses) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-t px-6 py-4 font-bold text-2xl  text-left">
                                Gross Profit
                            </td>
                            <td class="border-t px-6 py-4 font-bold text-2xl text-right">
                                {{ $filters.formatNumber(totalOperatingIncome - totalExpenses) }}
                            </td>
                        </tr>
                        <tr class="">
                            <td class="border-t  text-gray-400 px-6 py-4" colspan="2">
                                Operating Expense
                            </td>
                        </tr>
                        <tr v-for="(result,index) in results.other_expenses" :key="index"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t px-6 py-4 text-left">
                               <span class="">
                                        {{ result.name }}
                               </span>
                            </td>
                            <td class="border-t px-6 py-4  text-right">
                                <span class="">
                                    {{ $filters.formatNumber(result.balance) }}
                                </span>
                            </td>

                        </tr>
                        <tr>
                            <td class="border-t px-6 py-4 font-bold text-xl  text-left">
                                Total Operating Expense
                            </td>
                            <td class="border-t px-6 py-4 font-bold text-xl text-right">
                                {{ $filters.formatNumber(totalOtherExpenses) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-t px-6 py-4 font-bold text-2xl  text-left">
                                Operating Profit
                            </td>
                            <td class="border-t px-6 py-4 font-bold text-2xl text-right">
                                {{ $filters.formatNumber(totalOperatingIncome - totalExpenses - totalOtherExpenses) }}
                            </td>
                        </tr>
                        <tr class="">
                            <td class="border-t  text-gray-400 px-6 py-4" colspan="2">
                                Non Operating Income
                            </td>
                        </tr>
                        <tr v-for="(result,index) in results.other_income" :key="index"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t px-6 py-4 text-left">
                               <span class="">
                                        {{ result.name }}
                               </span>
                            </td>
                            <td class="border-t px-6 py-4  text-right">
                                <span class="">
                                    {{ $filters.formatNumber(result.balance) }}
                                </span>
                            </td>

                        </tr>
                        <tr>
                            <td class="border-t px-6 py-4 font-bold text-xl  text-left">
                                Total Non Operating Income
                            </td>
                            <td class="border-t px-6 py-4 font-bold text-xl text-right">
                                {{ $filters.formatNumber(totalOtherIncome) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-t px-6 py-4 font-bold text-2xl  text-left">
                                Net Profit/Loss
                            </td>
                            <td class="border-t px-6 py-4 font-bold text-2xl text-right">
                                {{
                                    $filters.formatNumber(totalOperatingIncome + totalOtherIncome - totalExpenses - totalOtherExpenses)
                                }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <teleport to="head">
            <title>Income Statement</title>
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


export default {
    props: {
        report: Object,
        results: Object,
        filters: Object,
        branches: Object,
        currencies: Object,
        financialPeriods: Object,
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
                financial_period_id: this.filters.financial_period_id,
                start_date: this.filters.start_date,
                end_date: this.filters.end_date,
                branch_id: this.filters.branch_id,
                currency_id: this.filters.currency_id,
                status: this.filters.status,
                processing: false,
            },
            totalOperatingIncome: 0,
            totalExpenses: 0,
            totalOtherExpenses: 0,
            totalOtherIncome: 0,
        }

    },
    mounted() {
        this.totalAssetsBalance = 0;
        this.totalLiabilitiesBalance = 0;
        this.totalEquityBalance = 0;
        this.results.income.forEach(item => {
            this.totalOperatingIncome += parseFloat(item.balance);
        })
        this.results.expenses.forEach(item => {
            this.totalExpenses += parseFloat(item.balance);
        })
        this.results.other_expenses.forEach(item => {
            this.totalOtherExpenses += parseFloat(item.balance);
        })
        this.results.other_income.forEach(item => {
            this.totalOtherIncome += parseFloat(item.balance);
        })
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
            let query = pickBy(this.form)
            this.$inertia.get(this.route('reports.accounting.income_statement', Object.keys(query).length ? query : {}))
        },
        apply() {
            let query = pickBy(this.form)
            this.$inertia.get(this.route('reports.accounting.income_statement', Object.keys(query).length ? query : {}))
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
