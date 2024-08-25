<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.transactions.index',loan.id)">Loan
                    #{{ loan.id }}
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Transaction Details
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <table class="w-full whitespace-no-wrap table-auto">
                    <tbody>
                    <tr
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            ID
                        </td>
                        <td class="border-t px-6 py-4">
                            {{ transaction.id }}
                        </td>
                    </tr>
                    <tr
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            Type
                        </td>
                        <td class="border-t px-6 py-4">
                            {{ transaction.type.name }}
                        </td>
                    </tr>
                    <tr
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            Date
                        </td>
                        <td class="border-t px-6 py-4">
                            {{ transaction.submitted_on }}
                        </td>
                    </tr>
                    <tr
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            Amount
                        </td>
                        <td class="border-t px-6 py-4">
                            {{ $filters.currency(transaction.amount) }}
                        </td>
                    </tr>
                    <tr v-if="transaction.payment_detail"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4" colspan="2">
                            <b>Payment Details</b>
                        </td>
                    </tr>
                    <tr v-if="transaction.payment_detail && transaction.payment_detail.payment_type"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            Payment Type
                        </td>
                        <td class="border-t px-6 py-4">
                            {{ transaction.payment_detail.payment_type.name }}
                        </td>
                    </tr>
                    <tr v-if="transaction.payment_detail && transaction.payment_detail.account_number"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            Account Number
                        </td>
                        <td class="border-t px-6 py-4">
                            {{ transaction.payment_detail.account_number }}
                        </td>
                    </tr>
                    <tr v-if="transaction.payment_detail && transaction.payment_detail.cheque_number"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            Cheque Number
                        </td>
                        <td class="border-t px-6 py-4">
                            {{ transaction.payment_detail.cheque_number }}
                        </td>
                    </tr>
                    <tr v-if="transaction.payment_detail && transaction.payment_detail.routing_code"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4">
                            Routing Code
                        </td>
                        <td class="border-t px-6 py-4">
                            {{ transaction.payment_detail.routing_code }}
                        </td>
                    </tr>
                    </tbody>
                </table>
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
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

export default {
    components: {
        AppLayout,
        TextareaInput,
        JetLabel,
        JetInput,
        JetInputError,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        JetButton,
        JetCheckbox,
        FileInput,
    },
    props: {
        loan: Object,
        paymentTypes: Object,
        transaction: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                payment_type_id: this.transaction.payment_detail.payment_type_id,
                amount: this.transaction.amount,
                date: this.transaction.submitted_on,
                description: this.transaction.payment_detail.description,
                cheque_number: this.transaction.payment_detail.cheque_number,
                receipt: this.transaction.payment_detail.receipt,
                account_number: this.transaction.payment_detail.account_number,
                bank_name: this.transaction.payment_detail.bank_name,
                routing_code: this.transaction.payment_detail.routing_code,
                custom_fields: this.transaction.custom_fields,
            }),
            pageTitle: "Loan Transaction Details",
            pageDescription: "Loan Transaction Details",

        }
    },

    methods: {
        submit() {
            this.form.put(this.route('loans.transactions.update', this.transaction.id), {})
        },
    },
}
</script>

<style scoped>

</style>
