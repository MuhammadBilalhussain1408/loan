<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('accounting.chart_of_accounts.index')">Chart of Accounts
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
            </h2>
        </template>

        <div class="">
            <div class=" mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <jet-label for="parent_id" value="Parent"/>
                                <Multiselect
                                    v-model="form.parent_id"
                                    mode="single"
                                    :options="$page.props.chartOfAccounts"
                                />
                                <jet-input-error :message="form.errors.parent_id" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="name" value="Name"/>
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                           required autofocus/>
                                <jet-input-error :message="form.errors.name" class="mt-2" required/>
                            </div>
                            <div class="">
                                <jet-label for="gl_code" value="GL Code"/>
                                <jet-input id="gl_code" type="text" class="mt-1 block w-full" v-model="form.gl_code"/>
                                <jet-input-error :message="form.errors.gl_code" class="mt-2" required/>
                            </div>
                            <div>
                                <jet-label for="account_type" value="Account Type"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                    name="account_type" v-model="form.account_type" id="account_type">
                                    <option value="asset">Asset</option>
                                    <option value="expense">Expense</option>
                                    <option value="equity">Equity</option>
                                    <option value="liability">Liability</option>
                                    <option value="income">Income</option>
                                </select>
                                <jet-input-error :message="form.errors.account_type" class="mt-2"/>
                            </div>

                            <div class="">
                                <jet-label for="external_id" value="External ID"/>
                                <jet-input id="external_id" type="text" class="mt-1 block w-full"
                                           v-model="form.external_id"/>
                                <jet-input-error :message="form.errors.external_id" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="allow_manual">
                                    <div class="flex items-center">
                                        <jet-checkbox name="enable_reconciliation" id="allow_manual"
                                                      v-model:checked="form.allow_manual"/>
                                        <div class="ml-2">
                                            Allow Manual Entries
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                            <div>
                                <jet-label for="enable_reconciliation">
                                    <div class="flex items-center">
                                        <jet-checkbox name="enable_reconciliation" id="enable_reconciliation"
                                                      v-model:checked="form.enable_reconciliation"/>
                                        <div class="ml-2">
                                            Enable Reconciliation
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
        chartOfAccounts:Object,
        chartOfAccount: Object
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
                parent_id: this.chartOfAccount.parent_id,
                name: this.chartOfAccount.name,
                gl_code: this.chartOfAccount.gl_code,
                external_id: this.chartOfAccount.external_id,
                account_type: this.chartOfAccount.account_type,
                description: this.chartOfAccount.description,
                enable_reconciliation: this.chartOfAccount.enable_reconciliation,
                allow_manual: this.chartOfAccount.allow_manual,
                active: this.chartOfAccount.active,
            }),
            pageTitle: "Edit Chart of Account",
            pageDescription: "Edit Chart of Account",
        }

    },
    methods: {
        submit() {
            this.form.put(this.route('accounting.chart_of_accounts.update',this.chartOfAccount.id), {})

        },

    }
}
</script>
<style scoped>

</style>
