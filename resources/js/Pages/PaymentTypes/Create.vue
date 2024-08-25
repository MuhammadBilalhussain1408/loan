<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('payment_types.index')">Payment
                    Types
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
                                           required autofocus/>
                                <jet-input-error :message="form.errors.name" class="mt-2"/>
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
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        chartOfAccounts:Object
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
                name: null,
                position: null,
                chart_of_account_debit_id: null,
                chart_of_account_credit_id: null,
                description: null,
                report_color: null,
                is_cash: false,
                active: true,
            }),
            pageTitle: "Create Payment Type",
            pageDescription: "Create Payment Type",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('payment_types.store'), {})

        },

    }
}
</script>
<style scoped>

</style>
