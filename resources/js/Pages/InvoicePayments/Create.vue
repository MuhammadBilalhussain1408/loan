<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('billing.invoices.index')">
                    Billing/
                </inertia-link>
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('billing.invoices.show',invoice.id)">
                    Invoice
                    #{{ invoice.id }}
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Create
            </h2>
        </template>
        <div class="">
            <div class=" mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <jet-label for="paid_by" value="Paid By"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                    name="paid_by" v-model="form.paid_by" id="paid_by">
                                    <option value="patient">Patient</option>
                                    <option value="co_payer">CoPayer</option>
                                </select>
                                <jet-input-error :message="form.errors.paid_by" class="mt-2"/>
                            </div>
                            <div v-if="form.paid_by=='co_payer'">
                                <jet-label for="co_payer_id" value="CoPayer"/>
                                <Multiselect
                                    id="co_payer_id"
                                    v-model="form.co_payer_id"
                                    mode="single"
                                    :searchable="true"
                                    :options="$page.props.coPayers"
                                />
                                <jet-input-error :message="form.errors.co_payer_id" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="date" value="Date"/>
                                <flat-pickr
                                    v-model="form.date"
                                    class="form-control w-full"
                                    placeholder="Select date"
                                    name="date">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.date" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="amount" value="Amount"/>
                                <jet-input id="code" type="text" class="mt-1 block w-full"
                                           v-model="form.amount"/>
                                <jet-input-error :message="form.errors.amount" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="payment_type_id" value="Payment Type"/>
                                <Multiselect
                                    id="payment_type_id"
                                    v-model="form.payment_type_id"
                                    mode="single"
                                    :searchable="true"
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
                            <div>
                                <jet-label for="print_payment">
                                    <div class="flex items-center">
                                        <jet-checkbox name="print_payment" id="print_payment"
                                                      v-model:checked="form.print_payment"/>
                                        <div class="ml-2">
                                            Print Payment
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
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        currencies: Object,
        invoice: Object,
        paymentTypes: Object,
        coPayers: Object,
    },
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
                paid_by: 'patient',
                co_payer_id: this.invoice.co_payer_id,
                trans_id: null,
                amount: null,
                date: moment().format("YYYY-MM-DD"),
                payment_type_id: null,
                reference: null,
                account_number: null,
                routing_code: null,
                receipt: null,
                bank_name: null,
                description: null,
                print_payment: true,
            }),
            pageTitle: "Create Payment",
            pageDescription: "Create Payment",
        }

    },
    mounted() {
        if (this.invoice.sponsor === 'cash') {
            this.form.amount = this.invoice.cash_balance;
        }
    },
    methods: {
        submit() {
            this.form.post(this.route('billing.payments.store', this.invoice.id), {})

        },

    }
}
</script>
<style scoped>

</style>
