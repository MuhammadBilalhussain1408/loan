<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.products.index')">Loan
                    Products
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
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
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-4">
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
        product: Object,
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
                currency_id: this.product.currency_id,
                loan_disbursement_channel_id: this.product.loan_disbursement_channel_id,
                loan_transaction_processing_strategy_id: this.product.loan_transaction_processing_strategy_id,
                loan_application_checklist_id: this.product.loan_application_checklist_id,
                fund_source_chart_of_account_id: this.product.fund_source_chart_of_account_id,
                loan_portfolio_chart_of_account_id: this.product.loan_portfolio_chart_of_account_id,
                interest_receivable_chart_of_account_id: this.product.interest_receivable_chart_of_account_id,
                penalties_receivable_chart_of_account_id: this.product.penalties_receivable_chart_of_account_id,
                fees_receivable_chart_of_account_id: this.product.fees_receivable_chart_of_account_id,
                fees_chart_of_account_id: this.product.fees_chart_of_account_id,
                overpayments_chart_of_account_id: this.product.overpayments_chart_of_account_id,
                suspended_income_chart_of_account_id: this.product.suspended_income_chart_of_account_id,
                income_from_interest_chart_of_account_id: this.product.income_from_interest_chart_of_account_id,
                income_from_penalties_chart_of_account_id: this.product.income_from_penalties_chart_of_account_id,
                income_from_fees_chart_of_account_id: this.product.income_from_fees_chart_of_account_id,
                income_from_recovery_chart_of_account_id: this.product.income_from_recovery_chart_of_account_id,
                losses_written_off_chart_of_account_id: this.product.losses_written_off_chart_of_account_id,
                interest_written_off_chart_of_account_id: this.product.interest_written_off_chart_of_account_id,
                name: this.product.name,
                short_name:this.product.short_name,
                description: this.product.description,
                decimals: this.product.decimals,
                instalment_multiple_of: this.product.instalment_multiple_of,
                minimum_principal: this.product.minimum_principal,
                default_principal: this.product.default_principal,
                maximum_principal: this.product.maximum_principal,
                minimum_loan_term:this.product.minimum_loan_term,
                default_loan_term: this.product.default_loan_term,
                maximum_loan_term: this.product.maximum_loan_term,
                repayment_frequency: this.product.repayment_frequency,
                repayment_frequency_type: this.product.repayment_frequency_type,
                minimum_interest_rate: this.product.minimum_interest_rate,
                default_interest_rate: this.product.default_interest_rate,
                maximum_interest_rate: this.product.maximum_interest_rate,
                interest_rate_type: this.product.interest_rate_type,
                enable_balloon_payments: this.product.enable_balloon_payments,
                allow_schedule_adjustments: this.product.allow_schedule_adjustments,
                grace_on_principal_paid: this.product.grace_on_principal_paid,
                grace_on_interest_paid: this.product.grace_on_interest_paid,
                grace_on_interest_charged: this.product.grace_on_interest_charged,
                allow_custom_grace_period: this.product.allow_custom_grace_period,
                allow_topup: this.product.allow_topup,
                interest_methodology: this.product.interest_methodology,
                interest_recalculation: this.product.interest_recalculation,
                amortization_method: this.product.amortization_method,
                interest_calculation_period_type:this.product.interest_calculation_period_type,
                days_in_year: this.product.days_in_year,
                days_in_month: this.product.days_in_month,
                include_in_loan_cycle: this.product.include_in_loan_cycle,
                lock_guarantee_funds: this.product.lock_guarantee_funds,
                auto_allocate_overpayments: this.product.auto_allocate_overpayments,
                allow_additional_charges: this.product.allow_additional_charges,
                auto_disburse: this.product.auto_disburse,
                require_linked_savings_account: this.product.require_linked_savings_account,
                min_amount: this.product.min_amount,
                max_amount: this.product.max_amount,
                accounting_rule: this.product.accounting_rule,
                npa_overdue_days: this.product.npa_overdue_days,
                npa_suspend_accrued_income: this.product.npa_suspend_accrued_income,
                deduct_interest_from_principal: this.product.deduct_interest_from_principal,
                disallow_interest_rate_adjustment: this.product.disallow_interest_rate_adjustment,
                exclude_weekends: this.product.exclude_weekends,
                exclude_holidays: this.product.exclude_holidays,
                active: this.product.active,
                selected_charges:this.product.selected_charges,
            }),
            pageTitle: "Edit Loan Product",
            pageDescription: "Edit Loan Product",
        }

    },
    methods: {
        submit() {
            this.form.put(this.route('loans.products.update',this.product.id), {})

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
