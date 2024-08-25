<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('accounting.journal_entries.index')">
                    Journal Entries
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Create
            </h2>
        </template>

        <div class="">
            <div class=" mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-2">
                            <div class="">
                                <jet-label for="amount" value="Amount"/>
                                <jet-input id="amount" type="text" class="mt-1 block w-full" v-model="form.amount"
                                           required autofocus/>
                                <jet-input-error :message="form.errors.amount" class="mt-2" required/>
                            </div>
                            <div>
                                <jet-label for="currency_id" value="Currency"/>
                                <Multiselect
                                    v-model="form.currency_id"
                                    mode="single"
                                    :required="true"
                                    :options="$page.props.currencies"
                                />
                                <jet-input-error :message="form.errors.currency_id" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="date" value="Date"/>
                                <flat-pickr
                                    v-model="form.date"
                                    class="form-control w-full"
                                    placeholder="Select date"
                                    required
                                    name="date">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.date" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="debit" value="Account to be Debited"/>
                                <Multiselect
                                    v-model="form.debit"
                                    mode="single"
                                    :required="true"
                                    :options="$page.props.chartOfAccounts"
                                />
                                <jet-input-error :message="form.errors.debit" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="credit" value="Account to be Credited"/>
                                <Multiselect
                                    v-model="form.credit"
                                    mode="single"
                                    :required="true"
                                    :options="$page.props.chartOfAccounts"
                                />
                                <jet-input-error :message="form.errors.credit" class="mt-2"/>
                            </div>

                            <div class="">
                                <jet-label for="reference" value="Reference"/>
                                <jet-input id="reference" type="text" class="mt-1 block w-full"
                                           v-model="form.reference"/>
                                <jet-input-error :message="form.errors.reference" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="payment_type_id" value="Payment Type"/>
                                <Multiselect
                                    v-model="form.payment_type_id"
                                    mode="single"
                                    :options="$page.props.paymentTypes"
                                />
                                <jet-input-error :message="form.errors.payment_type_id" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="account_number" value="Account Number"/>
                                <jet-input id="account_number" type="text" class="mt-1 block w-full"
                                           v-model="form.account_number"/>
                                <jet-input-error :message="form.errors.account_number" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="routing_code" value="Routing Code"/>
                                <jet-input id="routing_code" type="text" class="mt-1 block w-full"
                                           v-model="form.routing_code"/>
                                <jet-input-error :message="form.errors.routing_code" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="receipt" value="Receipt"/>
                                <jet-input id="receipt" type="text" class="mt-1 block w-full"
                                           v-model="form.receipt"/>
                                <jet-input-error :message="form.errors.receipt" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="bank_name" value="Bank Name"/>
                                <jet-input id="bank_name" type="text" class="mt-1 block w-full"
                                           v-model="form.bank_name"/>
                                <jet-input-error :message="form.errors.bank_name" class="mt-2"/>
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
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        chartOfAccounts: Object,
        paymentTypes: Object,
        currencies: Object,
        currency: Object,  },
    components: {
        Select,
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
                debit: null,
                credit: null,
                amount: null,
                currency_id: this.$page.props.currency.id,
                date: moment().format("YYYY-MM-DD"),
                payment_type_id: null,
                reference: null,
                account_number: null,
                routing_code: null,
                receipt: null,
                bank_name: null,
                description: null,

            }),
            pageTitle: "Create Journal Entry",
            pageDescription: "Create Journal Entry",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('accounting.journal_entries.store'), {})

        },

    }
}
</script>
<style scoped>

</style>
