<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.linked_charges.index',loan.id)">Loan
                    #{{ loan.id }}
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Add Charge
            </h2>
        </template>

        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <jet-label for="loan_charge_id" value="Charge"/>
                            <select-input id="loan_charge_id" class="mt-1 block w-full" @change="changeCharge"
                                          v-model="form.loan_charge_id" required>
                                <option v-for="item in availableCharges" :value="item.id">{{ item.name }}</option>
                            </select-input>
                            <jet-input-error :message="form.errors.loan_charge_id" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="amount" value="Amount"/>
                            <jet-input id="amount" type="text" class="mt-1 block w-full"
                                       v-model="form.amount" required/>
                            <jet-input-error :message="form.errors.amount" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="date" value="Date"/>
                            <flat-pickr
                                v-model="form.date"
                                class="form-control w-full"
                                placeholder="Select date">
                            </flat-pickr>
                            <jet-input-error :message="form.errors.date" class="mt-2"/>
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
    },
    data() {
        return {
            form: this.$inertia.form({
                loan_charge_id: null,
                amount: null,
                date: moment().format("YYYY-MM-DD"),
            }),
            pageTitle: "Create Loan Charge",
            pageDescription: "Create Loan Charge",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('loans.linked_charges.store', this.loan.id), {})
        },
        changeCharge() {
            if (this.form.loan_charge_id) {
                this.loan.product.charges.forEach(item => {
                    if (item.charge && item.charge.type && item.charge.type.name === 'Specified Due Date' && item.charge.id == this.form.loan_charge_id) {
                        this.form.amount = item.charge.amount
                    }
                })
            }
        }
    },
    computed: {
        availableCharges: function () {
            let available = []
            this.loan.product.charges.forEach(item => {
                if (item.charge && item.charge.type && item.charge.type.name === 'Specified Due Date') {
                    available.push({
                        id: item.loan_charge_id,
                        name: item.charge.name
                    })
                }
            })
            return available
        }
    },
}
</script>

<style scoped>

</style>
