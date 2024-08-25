<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.charges.index')">Loan
                    Charges
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
                                <jet-label for="name" value="Name"/>
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                           required/>
                                <jet-input-error :message="form.errors.name" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="loan_charge_type_id" value="Charge Type"/>
                                <select-input id="loan_charge_type_id" class="mt-1 block w-full"
                                              v-model="form.loan_charge_type_id" required>
                                    <option v-for="item in types" :value="item.id">{{ item.name }}</option>
                                </select-input>
                                <jet-input-error :message="form.errors.loan_charge_type_id" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="amount" value="Amount"/>
                                <jet-input id="amount" type="text" class="mt-1 block w-full"
                                           v-model="form.amount" required/>
                                <jet-input-error :message="form.errors.amount" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="loan_charge_option_id" value="Charge Option"/>
                                <select-input id="loan_charge_option_id" class="mt-1 block w-full"
                                              v-model="form.loan_charge_option_id" required>
                                    <option v-for="item in options" :value="item.id">{{ item.name }}</option>
                                </select-input>
                                <jet-input-error :message="form.errors.loan_charge_option_id" class="mt-2"/>
                            </div>
                            <div>
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
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <div class="mt-4">
                                    <jet-label for="is_penalty">
                                        <div class="flex items-center">
                                            <jet-checkbox name="is_penalty" id="is_penalty"
                                                          v-model:checked="form.is_penalty"/>
                                            <div class="ml-2">
                                                Penalty
                                            </div>
                                        </div>
                                    </jet-label>
                                </div>
                                <div class="mt-4">
                                    <jet-label for="allow_override">
                                        <div class="flex items-center">
                                            <jet-checkbox name="allow_override" id="allow_override"
                                                          v-model:checked="form.allow_override"/>
                                            <div class="ml-2">
                                                Allow Override
                                            </div>
                                        </div>
                                    </jet-label>
                                </div>
                                <div class="mt-4">
                                    <jet-label for="active">
                                        <div class="flex items-center">
                                            <jet-checkbox name="active" id="active" v-model:checked="form.active"/>
                                            <div class="ml-2">
                                                Active
                                            </div>
                                        </div>
                                    </jet-label>
                                </div>
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
        types: Object,
        options: Object,
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
                loan_charge_type_id: null,
                loan_charge_option_id: null,
                name: null,
                amount: null,
                min_amount: null,
                max_amount: null,
                payment_mode: 'regular',
                schedule: false,
                schedule_frequency: '',
                schedule_frequency_type: '',
                is_penalty: false,
                allow_override: false,
                active: true,
                description: null,
            }),
            pageTitle: "Create Loan Charge",
            pageDescription: "Create Loan Charge",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('loans.charges.store'), {})

        },

    }
}
</script>
<style scoped>

</style>
