<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('payment_types.index')">Payment
                    Types
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
            </h2>
        </template>

        <div class="">
            <div class=" mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-2">
                            <div class="">
                                <jet-label for="name" value="Name"/>
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                           required autofocus/>
                                <jet-input-error :message="form.errors.name" class="mt-2"/>
                            </div>
                            <div v-if="paymentType.system_name==='paypal'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="">
                                    <jet-label for="email" value="Email"/>
                                    <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.options.email"/>
                                </div>
                                <div class="">
                                    <jet-label for="currency_code" value="Currency Code"/>
                                    <jet-input id="currency_code" type="text" class="mt-1 block w-full" v-model="form.options.currency_code"/>
                                </div>
                                <div class="">
                                    <jet-label for="sandbox" value="Sandbox Mode"/>
                                    <select-input id="sandbox" class="mt-1 block w-full" v-model="form.options.test_mode">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select-input>
                                </div>
                            </div>
                            <div v-if="paymentType.system_name==='stripe'" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="">
                                    <jet-label for="publishable_key" value="Publishable Key"/>
                                    <jet-input id="publishable_key" type="text" class="mt-1 block w-full" v-model="form.options.publishable_key"/>
                                </div>
                                <div class="">
                                    <jet-label for="secret_key" value="Secret Key"/>
                                    <jet-input id="secret_key" type="text" class="mt-1 block w-full" v-model="form.options.secret_key"/>
                                </div>
                                <div class="">
                                    <jet-label for="webhook_signing_secret" value="Webhook Signing Secret"/>
                                    <jet-input id="webhook_signing_secret" type="text" class="mt-1 block w-full" v-model="form.options.webhook_signing_secret"/>
                                </div>
                                <div class="">
                                    <jet-label for="currency_code" value="Currency Code"/>
                                    <jet-input id="currency_code" type="text" class="mt-1 block w-full" v-model="form.options.currency_code"/>
                                </div>
                            </div>
                            <div v-if="paymentType.system_name==='paynow'" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="">
                                    <jet-label for="usd_integration_id" value="USD Integration ID"/>
                                    <jet-input id="usd_integration_id" type="text" class="mt-1 block w-full" v-model="form.options.usd_integration_id"/>
                                </div>
                                <div class="">
                                    <jet-label for="usd_integration_key" value="USD Integration Key"/>
                                    <jet-input id="usd_integration_key" type="text" class="mt-1 block w-full" v-model="form.options.usd_integration_key"/>
                                </div>
                                <div class="">
                                    <jet-label for="rtgs_integration_id" value="RTGS Integration ID"/>
                                    <jet-input id="rtgs_integration_id" type="text" class="mt-1 block w-full" v-model="form.options.rtgs_integration_id"/>
                                </div>
                                <div class="">
                                    <jet-label for="rtgs_integration_key" value="RTGS Integration Key"/>
                                    <jet-input id="rtgs_integration_key" type="text" class="mt-1 block w-full" v-model="form.options.rtgs_integration_key"/>
                                </div>
                            </div>
                            <div class="">
                                <jet-label for="position" value="Position"/>
                                <jet-input id="position" type="number" class="mt-1 block w-full"
                                           v-model="form.position"/>
                                <jet-input-error :message="form.errors.position" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="report_color" value="Report Color"/>
                                <jet-input id="report_color" type="text" class="mt-1 block w-full" v-model="form.report_color"/>
                                <jet-input-error :message="form.errors.report_color" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="chart_of_account_debit_id" value="Chart of Account to be debited"/>
                                <Multiselect
                                    v-model="form.chart_of_account_debit_id"
                                    mode="single"
                                    :options="$page.props.chartOfAccounts"
                                />
                                <jet-input-error :message="form.errors.chart_of_account_debit_id" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="chart_of_account_credit_id" value="Chart of Account to be credited"/>
                                <Multiselect
                                    v-model="form.chart_of_account_credit_id"
                                    mode="single"
                                    :options="$page.props.chartOfAccounts"
                                />
                                <jet-input-error :message="form.errors.chart_of_account_credit_id" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="is_cash">
                                    <div class="flex items-center">
                                        <jet-checkbox name="is_cash" id="is_cash" v-model:checked="form.is_cash"/>
                                        <div class="ml-2">
                                            Cash
                                        </div>
                                    </div>
                                </jet-label>
                            </div>

                            <div>
                                <jet-label for="active">
                                    <div class="flex items-center">
                                        <jet-checkbox name="active" id="active" v-model:checked="form.active"/>
                                        <div class="ml-2">
                                            Active
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                            <div>
                                <jet-label for="description" value="Description"/>
                                <textarea-input id="description" class="mt-1 block w-full"
                                                v-model="form.description"/>
                                <jet-input-error :message="form.errors.description" class="mt-2"/>

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
        paymentType: Object,
        chartOfAccounts: Object
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
                name: this.paymentType.name,
                position: this.paymentType.position,
                chart_of_account_debit_id: this.paymentType.chart_of_account_debit_id,
                chart_of_account_credit_id: this.paymentType.chart_of_account_credit_id,
                report_color: this.paymentType.report_color,
                description: this.paymentType.description,
                options: this.paymentType.options,
                is_cash: this.paymentType.is_cash,
                active: this.paymentType.active,
            }),
            pageTitle: "Edit Payment Type",
            pageDescription: "Edit Payment Type",
        }

    },
    methods: {
        submit() {
            this.form.put(this.route('payment_types.update', this.paymentType.id), {})

        },

    }
}
</script>
<style scoped>

</style>
