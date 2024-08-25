<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('billing.invoices.index')">Billing
                </inertia-link>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('billing.invoices.show',invoice.id)">
                    <span class="text-indigo-400 font-medium">/</span>Invoice
                    #{{ invoice.id }}
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Reconcile
            </h2>
        </template>


            <div class=" mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit">
                        <div>
                            <jet-label for="record_payment">
                                <div class="flex items-center">
                                    <jet-checkbox name="record_payment" id="record_payment"
                                                  v-model:checked="form.record_payment"/>
                                    <div class="ml-2">
                                        Record Payment
                                    </div>
                                </div>
                            </jet-label>
                        </div>
                        <div v-if="form.record_payment">
                            <div class="grid grid-cols-1 gap-2 mt-4">
                                <div>
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
                                    <jet-label for="receipt" value="Receipt"/>
                                    <jet-input id="receipt" type="text" class="mt-1 block w-full" v-model="form.receipt"/>
                                    <jet-input-error :message="form.errors.receipt" class="mt-2"/>
                                </div>
                                <div>
                                    <jet-label for="description" value="Description"/>
                                    <textarea-input id="description" class="mt-1 block w-full"
                                                    v-model="form.description"/>
                                    <jet-input-error :message="form.errors.description" class="mt-2"/>

                                </div>
                            </div>
                        </div>
                        <div class="mt-4 mb-4 overflow-x-scroll">
                            <table class="w-full whitespace-no-wrap">
                                <thead class="bg-gray-50">
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Qty</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Unit Cost</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Claimed Amount</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Award</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Shortfall</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Shortfall Reason</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in form.items">
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="item.name" readonly/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="item.qty" readonly/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="item.unit_cost" readonly/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="item.co_payer_amount" readonly/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="item.award" @input="updateItems" required
                                                   autocomplete="off"/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="item.shortfall" readonly autocomplete="off"/>
                                    </td>
                                    <td class="border-t">
                                        <jet-input type="text" class="mt-1 block w-full"
                                                   v-model="item.shortfall_reason" autocomplete="off"/>
                                    </td>

                                </tr>
                                </tbody>
                            </table>
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
                record_payment: true,
                paid_by: 'patient',
                co_payer_id: this.invoice.co_payer_id,
                payment_type_id: null,
                trans_id: null,
                receipt: null,
                amount: null,
                date: moment().format("YYYY-MM-DD"),
                items: this.invoice.invoice_items,
            }),
            pageTitle: "Reconcile Invoice",
            pageDescription: "Reconcile Invoice",
        }

    },
    mounted() {
        this.updateItems();
    },
    methods: {
        submit() {
            this.form.put(this.route('billing.invoices.reconcile.update', this.invoice.id), {})

        },
        updateItems() {
            this.form.items.forEach(item => {
                if (item.award) {
                    item.shortfall = parseFloat(item.co_payer_amount || 0) - parseFloat(item.award || 0);
                }

            });
        },


    }
}
</script>
<style scoped>

</style>
