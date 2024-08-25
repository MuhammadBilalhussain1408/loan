<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.products.index')">Loan
                    Products
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Create
            </h2>
        </template>

        <div class="">
            <div class=" mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div class="">
                                <jet-label for="name" value="Name"/>
                                <jet-input id="name" type="text" class="block w-full" v-model="form.name" autofocus
                                           required/>
                                <jet-input-error :message="form.errors.name" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="short_name" value="Short Name"/>
                                <jet-input id="short_name" type="text" class="block w-full" v-model="form.short_name"
                                           required/>
                                <jet-input-error :message="form.errors.short_name" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="description" value="Description"/>
                                <jet-input id="description" type="text" class="block w-full"
                                           v-model="form.description"/>
                                <jet-input-error :message="form.errors.description" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-4">
                            <div class="">
                                <jet-label for="loan_application_checklist_id" value="Checklist"/>
                                <select-input id="loan_application_checklist_id" class="block w-full"
                                              v-model="form.loan_application_checklist_id">
                                    <option v-for="item in checklists" :value="item.id">{{ item.name }}</option>
                                </select-input>
                                <jet-input-error :message="form.errors.loan_application_checklist_id" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="currency_id" value="Currency"/>
                                <Multiselect
                                    id="currency_id"
                                    v-model="form.currency_id"
                                    mode="single"
                                    value-prop="id"
                                    :searchable="true"
                                    label="name"
                                    :required="true"
                                    :options="currencies"
                                />
                                <jet-input-error :message="form.errors.currency_id" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="decimals" value="Decimals"/>
                                <jet-input id="decimals" type="number" class="block w-full"
                                           v-model="form.decimals"/>
                                <jet-input-error :message="form.errors.decimals" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-4">
                            <div class="">
                                <jet-label for="default_principal" value="Default Principal"/>
                                <jet-input id="default_principal" type="text" class="block w-full"
                                           v-model="form.default_principal"
                                           required/>
                                <jet-input-error :message="form.errors.default_principal" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="minimum_principal" value="Minimum Principal"/>
                                <jet-input id="minimum_principal" type="text" class="block w-full"
                                           v-model="form.minimum_principal"
                                           required/>
                                <jet-input-error :message="form.errors.minimum_principal" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="maximum_principal" value="Maximum Principal"/>
                                <jet-input id="maximum_principal" type="text" class="block w-full"
                                           v-model="form.maximum_principal" required/>
                                <jet-input-error :message="form.errors.maximum_principal" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-4">
                            <div class="">
                                <jet-label for="default_loan_term" value="Default Loan Term"/>
                                <jet-input id="default_loan_term" type="text" class="block w-full"
                                           v-model="form.default_loan_term"
                                           required/>
                                <jet-input-error :message="form.errors.default_loan_term" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="minimum_loan_term" value="Minimum Loan Term"/>
                                <jet-input id="minimum_loan_term" type="text" class="block w-full"
                                           v-model="form.minimum_loan_term"
                                           required/>
                                <jet-input-error :message="form.errors.minimum_loan_term" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="maximum_loan_term" value="Maximum Loan Term"/>
                                <jet-input id="maximum_loan_term" type="text" class="block w-full"
                                           v-model="form.maximum_loan_term" required/>
                                <jet-input-error :message="form.errors.maximum_loan_term" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div class="">
                                <jet-label for="repayment_frequency" value="Repayment Frequency"/>
                                <jet-input id="repayment_frequency" type="text" class="block w-full"
                                           v-model="form.repayment_frequency"
                                           required/>
                                <jet-input-error :message="form.errors.repayment_frequency" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="repayment_frequency_type" value="Repayment Frequency Type"/>
                                <select-input id="repayment_frequency_type" class="block w-full"
                                              v-model="form.repayment_frequency_type" required>
                                    <option value="days">Days</option>
                                    <option value="weeks">Weeks</option>
                                    <option value="months">Months</option>
                                </select-input>
                                <jet-input-error :message="form.errors.repayment_frequency_type" class="mt-2"/>
                            </div>

                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-4">
                            <div class="">
                                <jet-label for="default_interest_rate" value="Default Interest Rate"/>
                                <jet-input id="default_interest_rate" type="text" class="block w-full"
                                           v-model="form.default_interest_rate"
                                           required/>
                                <jet-input-error :message="form.errors.default_interest_rate" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="minimum_interest_rate" value="Minimum Interest Rate"/>
                                <jet-input id="minimum_interest_rate" type="text" class="block w-full"
                                           v-model="form.minimum_interest_rate"
                                           required/>
                                <jet-input-error :message="form.errors.minimum_interest_rate" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="maximum_interest_rate" value="Maximum Interest Rate"/>
                                <jet-input id="maximum_interest_rate" type="text" class="block w-full"
                                           v-model="form.maximum_interest_rate" required/>
                                <jet-input-error :message="form.errors.maximum_interest_rate" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="interest_rate_type" value="Per"/>
                                <select-input id="interest_rate_type" class="block w-full"
                                              v-model="form.interest_rate_type" required>
                                    <option value="day">Day</option>
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                    <option value="year">Year</option>
                                    <option value="principal">Principal</option>
                                </select-input>
                                <jet-input-error :message="form.errors.interest_rate_type" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div>
                                <jet-label for="disallow_interest_rate_adjustment">
                                    <div class="flex items-center">
                                        <jet-checkbox name="disallow_interest_rate_adjustment"
                                                      id="disallow_interest_rate_adjustment"
                                                      v-model:checked="form.disallow_interest_rate_adjustment"/>
                                        <div class="ml-2">
                                            Disallow interest rate adjustment
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                            <div>
                                <jet-label for="deduct_interest_from_principal">
                                    <div class="flex items-center">
                                        <jet-checkbox name="deduct_interest_from_principal"
                                                      id="deduct_interest_from_principal"
                                                      v-model:checked="form.deduct_interest_from_principal"/>
                                        <div class="ml-2">
                                            Deduct interest from principal
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-4">
                            <div class="">
                                <jet-label for="grace_on_principal_paid" value="Grace On Principal Payment"/>
                                <jet-input id="grace_on_principal_paid" type="number" class="block w-full"
                                           v-model="form.grace_on_principal_paid"/>
                                <jet-input-error :message="form.errors.grace_on_principal_paid" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="grace_on_interest_paid" value="Grace On Interest Payment"/>
                                <jet-input id="grace_on_interest_paid" type="number" class="block w-full"
                                           v-model="form.grace_on_interest_paid"/>
                                <jet-input-error :message="form.errors.grace_on_interest_paid" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="grace_on_interest_charged" value="Grace On Interest Charged"/>
                                <jet-input id="grace_on_interest_charged" type="text" class="block w-full"
                                           v-model="form.grace_on_interest_charged"/>
                                <jet-input-error :message="form.errors.grace_on_interest_charged" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-4">
                            <div class="">
                                <jet-label for="interest_methodology" value="Interest Methodology"/>
                                <select-input id="interest_methodology" class="block w-full"
                                              v-model="form.interest_methodology" required>
                                    <option value="flat">Flat</option>
                                    <option value="declining_balance">Declining Balance</option>
                                </select-input>
                                <jet-input-error :message="form.errors.interest_methodology" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="amortization_method" value="Amortization Method"/>
                                <select-input id="amortization_method" class="block w-full"
                                              v-model="form.amortization_method" required>
                                    <option value="equal_installments">Equal Installments</option>
                                    <option value="equal_principal_payments">Equal Principal Payments</option>
                                </select-input>
                                <jet-input-error :message="form.errors.amortization_method" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="loan_transaction_processing_strategy_id"
                                           value="Loan Transaction Processing Strategy"/>
                                <select-input id="loan_transaction_processing_strategy_id" class="block w-full"
                                              v-model="form.loan_transaction_processing_strategy_id" required>
                                    <option v-for="item in transactionProcessingStrategies" :value="item.id">
                                        {{ item.name }}
                                    </option>
                                </select-input>
                                <jet-input-error :message="form.errors.loan_transaction_processing_strategy_id"
                                                 class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1  gap-2 mt-4">
                            <div class="">
                                <jet-label for="selected_charges" value="Charges"/>
                                <Multiselect
                                    id="selected_charges"
                                    v-model="form.selected_charges"
                                    placeholder="Select Charges"
                                    mode="tags"
                                    value-prop="id"
                                    :searchable="true"
                                    label="name"
                                    :options="availableCharges"
                                />
                                <jet-input-error :message="form.errors.currency_id" class="mt-2"/>
                            </div>
                        </div>
                        <h3 class="mt-4 font-bold">Accounting</h3>
                        <div class="mt-4">
                            <jet-label for="accounting_rule" value="Accounting Rule"/>
                            <select-input id="accounting_rule" class="block w-full"
                                          v-model="form.accounting_rule" required>
                                <option value="none">None</option>
                                <option value="cash">Cash</option>
                            </select-input>
                            <jet-input-error :message="form.errors.accounting_rule" class="mt-2"/>
                        </div>
                        <div v-if="form.accounting_rule==='cash'" class="mt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 ">
                                <div>
                                    <jet-label for="fund_source_chart_of_account_id" value="Fund Source"/>
                                    <Multiselect
                                        id="fund_source_chart_of_account_id"
                                        v-model="form.fund_source_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="assets"
                                    />
                                    <jet-input-error :message="form.errors.fund_source_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="loan_portfolio_chart_of_account_id" value="Loan Portfolio"/>
                                    <Multiselect
                                        id="loan_portfolio_chart_of_account_id"
                                        v-model="form.loan_portfolio_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="assets"
                                    />
                                    <jet-input-error :message="form.errors.loan_portfolio_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                                <div>
                                    <jet-label for="overpayments_chart_of_account_id" value="Overpayments"/>
                                    <Multiselect
                                        id="overpayments_chart_of_account_id"
                                        v-model="form.overpayments_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="liabilities"
                                    />
                                    <jet-input-error :message="form.errors.overpayments_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="suspended_income_chart_of_account_id" value="Suspended Income"/>
                                    <Multiselect
                                        id="suspended_income_chart_of_account_id"
                                        v-model="form.suspended_income_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="liabilities"
                                    />
                                    <jet-input-error :message="form.errors.suspended_income_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-4">
                                <div>
                                    <jet-label for="income_from_interest_chart_of_account_id"
                                               value="Income from Interest"/>
                                    <Multiselect
                                        id="income_from_interest_chart_of_account_id"
                                        v-model="form.income_from_interest_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="income"
                                    />
                                    <jet-input-error :message="form.errors.income_from_interest_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="income_from_penalties_chart_of_account_id"
                                               value="Income from penalties"/>
                                    <Multiselect
                                        id="income_from_penalties_chart_of_account_id"
                                        v-model="form.income_from_penalties_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="income"
                                    />
                                    <jet-input-error :message="form.errors.income_from_penalties_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="income_from_fees_chart_of_account_id"
                                               value="Income from fees"/>
                                    <Multiselect
                                        id="income_from_fees_chart_of_account_id"
                                        v-model="form.income_from_fees_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="income"
                                    />
                                    <jet-input-error :message="form.errors.income_from_fees_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="income_from_recovery_chart_of_account_id"
                                               value="Income from recovery"/>
                                    <Multiselect
                                        id="income_from_recovery_chart_of_account_id"
                                        v-model="form.income_from_recovery_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="income"
                                    />
                                    <jet-input-error :message="form.errors.income_from_recovery_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                                <div>
                                    <jet-label for="losses_written_off_chart_of_account_id" value="Losses Written Off"/>
                                    <Multiselect
                                        id="losses_written_off_chart_of_account_id"
                                        v-model="form.losses_written_off_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="expenses"
                                    />
                                    <jet-input-error :message="form.errors.losses_written_off_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="interest_written_off_chart_of_account_id"
                                               value="Interest Written Off"/>
                                    <Multiselect
                                        id="interest_written_off_chart_of_account_id"
                                        v-model="form.interest_written_off_chart_of_account_id"
                                        mode="single"
                                        value-prop="id"
                                        :searchable="true"
                                        label="name"
                                        :required="true"
                                        :options="expenses"
                                    />
                                    <jet-input-error :message="form.errors.interest_written_off_chart_of_account_id"
                                                     class="mt-2"/>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-4 ">
                            <div class="mt-4 hidden">
                                <jet-label for="exclude_holidays">
                                    <div class="flex items-center">
                                        <jet-checkbox name="exclude_holidays" id="exclude_holidays"
                                                      v-model:checked="form.exclude_holidays"/>
                                        <div class="ml-2">
                                            Exclude Holidays
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                            <div class="mt-4 hidden">
                                <jet-label for="exclude_weekends">
                                    <div class="flex items-center">
                                        <jet-checkbox name="exclude_weekends" id="exclude_weekends"
                                                      v-model:checked="form.exclude_weekends"/>
                                        <div class="ml-2">
                                            Exclude Weekends
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                            <div class="mt-4">
                                <jet-label for="auto_disburse">
                                    <div class="flex items-center">
                                        <jet-checkbox name="auto_disburse" id="auto_disburse"
                                                      v-model:checked="form.auto_disburse"/>
                                        <div class="ml-2">
                                            Auto Disburse
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                            <div class="mt-4">
                                <jet-label for="active">
                                    <div class="flex items-center">
                                        <jet-checkbox name="active" id="active"
                                                      v-model:checked="form.active"/>
                                        <div class="ml-2">
                                            Active
                                        </div>
                                    </div>
                                </jet-label>
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


export default {
    props: {
        currencies: Object,
        transactionProcessingStrategies: Object,
        checklists: Object,
        assets: Object,
        expenses: Object,
        income: Object,
        liabilities: Object,
        charges: Object,
        creditChecks: Object,
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
                currency_id: 1,
                loan_disbursement_channel_id: null,
                loan_transaction_processing_strategy_id: null,
                loan_application_checklist_id: null,
                fund_source_chart_of_account_id: null,
                loan_portfolio_chart_of_account_id: null,
                interest_receivable_chart_of_account_id: null,
                penalties_receivable_chart_of_account_id: null,
                fees_receivable_chart_of_account_id: null,
                fees_chart_of_account_id: null,
                overpayments_chart_of_account_id: null,
                suspended_income_chart_of_account_id: null,
                income_from_interest_chart_of_account_id: null,
                income_from_penalties_chart_of_account_id: null,
                income_from_fees_chart_of_account_id: null,
                income_from_recovery_chart_of_account_id: null,
                losses_written_off_chart_of_account_id: null,
                interest_written_off_chart_of_account_id: null,
                name: '',
                short_name: '',
                description: '',
                decimals: 0,
                instalment_multiple_of: '',
                minimum_principal: '',
                default_principal: '',
                maximum_principal: '',
                minimum_loan_term: '',
                default_loan_term: '',
                maximum_loan_term: '',
                repayment_frequency: '',
                repayment_frequency_type: '',
                minimum_interest_rate: '',
                default_interest_rate: '',
                maximum_interest_rate: '',
                interest_rate_type: 'year',
                enable_balloon_payments: false,
                allow_schedule_adjustments: false,
                grace_on_principal_paid: 0,
                grace_on_interest_paid: 0,
                grace_on_interest_charged: 0,
                allow_custom_grace_period: false,
                allow_topup: false,
                interest_methodology: 'declining_balance',
                interest_recalculation: false,
                amortization_method: 'equal_installments',
                interest_calculation_period_type: 'daily',
                days_in_year: 'actual',
                days_in_month: 'actual',
                include_in_loan_cycle: false,
                lock_guarantee_funds: false,
                auto_allocate_overpayments: false,
                allow_additional_charges: false,
                auto_disburse: false,
                require_linked_savings_account: false,
                min_amount: '',
                max_amount: '',
                accounting_rule: 'none',
                npa_overdue_days: 0,
                npa_suspend_accrued_income: false,
                deduct_interest_from_principal: false,
                disallow_interest_rate_adjustment: false,
                exclude_weekends: false,
                exclude_holidays: false,
                active: true,
                selected_charges: [],
            }),
            pageTitle: "Create Loan Product",
            pageDescription: "Create Loan Product",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('loans.products.store'), {})

        },

    },
    computed: {
        availableCharges: function () {
            let charges = [];
            if (this.form.currency_id) {
                charges = this.charges.filter(item => {
                    return item.currency_id == this.form.currency_id
                })
            }
            return charges;
        }
    }
}
</script>
<style scoped>

</style>
