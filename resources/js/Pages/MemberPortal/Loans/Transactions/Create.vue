<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('portal.loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('portal.loans.show',loan.id)">Loan
                    #{{ loan.id }}
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Pay Online
            </h2>
        </template>

        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="pay" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <jet-label for="amount" value="Amount"/>
                            <jet-input id="amount" type="text" class="mt-1 block w-full"
                                       v-model="form.amount" autofocus required/>
                        </div>
                        <div>
                            <jet-label for="payment_type_id" value="Payment Method"/>
                            <select-input id="payment_type_id" class="mt-1 block w-full" @change="selectPaymentType"
                                          v-model="form.payment_type_id" required>
                                <option v-for="item in paymentTypes" :value="item.id">{{ item.name }}</option>
                            </select-input>
                        </div>
                        <div v-if="selectedPaymentType">
                            <h4>{{ selectedPaymentType.description}}</h4>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                            Pay
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
import AppLayout from '@/Pages/MemberPortal/Layouts/AppLayout.vue'
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
        customFields: Object,

    },
    data() {
        return {
            form: {
                payment_type_id: null,
                amount: null,
                processing: false,
            },
            selectedPaymentType: null,
            pageTitle: "Pay Online",
            pageDescription: "Pay Online",

        }
    },

    methods: {
        pay() {
            this.form.processing = true
            if (this.selectedPaymentType) {
                axios.post(this.route('portal.loans.transactions.store'), this.form).then(response => {
                    window.location.href = response.data.url
                    this.form.processing = false
                }).catch(error => {
                    this.form.processing = false
                    this.$swal({
                        icon: 'error',
                        text: 'An error occurred, please try again later',
                        showCancelButton: false,
                        timer:4000
                    })
                })
            }
        },
        selectPaymentType() {
            this.paymentTypes.forEach(item => {
                if (item.id == this.form.payment_type_id) {
                    this.selectedPaymentType = item;
                }
            })
        },
    },
}
</script>

<style scoped>

</style>
