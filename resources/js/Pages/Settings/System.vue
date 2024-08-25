<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('settings.index')">Settings /
                </inertia-link>
                System
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <jet-label for="site_online" value="Site Online"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="site_online" v-model="form.site_online" id="site_online">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <jet-input-error :message="form.errors.site_online" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="allow_self_registration" value="Allow Client Self Registration"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="allow_self_registration" v-model="form.allow_self_registration"
                                id="allow_self_registration">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            <jet-input-error :message="form.errors.allow_self_registration" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="timezone" value="Timezone"/>
                            <Multiselect
                                id="timezone"
                                v-model="form.timezone"
                                mode="single"
                                :searchable="true"
                                :options="$page.props.timezones"
                            />
                            <jet-input-error :message="form.errors.timezone" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="currency" value="Currency"/>
                            <Multiselect
                                id="currency"
                                v-model="form.currency"
                                mode="single"
                                :searchable="true"
                                :options="$page.props.currencies"
                            />
                            <jet-input-error :message="form.errors.currency" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="loan_reference_prefix" value="Loan Reference Prefix"/>
                            <jet-input id="loan_reference_prefix" type="text" class="mt-1 block w-full"
                                       v-model="form.loan_reference_prefix"/>
                            <jet-input-error :message="form.errors.loan_reference_prefix" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="loan_reference_format" value="Loan Reference Format"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="loan_reference_format" v-model="form.loan_reference_format"
                                id="loan_reference_format">
                                <option value="YEAR/Sequence Number (SL/2014/001)">YEAR/Sequence Number (SL/2014/001)</option>
                                <option value="YEAR/MONTH/Sequence Number (SL/2014/08/001)">YEAR/MONTH/Sequence Number (SL/2014/08/001)</option>
                                <option value="Sequence Number">Sequence Number</option>
                                <option value="Random Number">Random Number</option>
                                <option value="Branch Product Sequence Number">Branch Product Sequence Number</option>
                            </select>
                            <jet-input-error :message="form.errors.loan_reference_format" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="savings_reference_prefix" value="Savings Reference Prefix"/>
                            <jet-input id="savings_reference_prefix" type="text" class="mt-1 block w-full"
                                       v-model="form.savings_reference_prefix"/>
                            <jet-input-error :message="form.errors.savings_reference_prefix" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="savings_reference_format" value="Loan Reference Format"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="savings_reference_format" v-model="form.savings_reference_format"
                                id="savings_reference_format">
                                <option value="YEAR/Sequence Number (SL/2014/001)">YEAR/Sequence Number (SL/2014/001)</option>
                                <option value="YEAR/MONTH/Sequence Number (SL/2014/08/001)">YEAR/MONTH/Sequence Number (SL/2014/08/001)</option>
                                <option value="Sequence Number">Sequence Number</option>
                                <option value="Random Number">Random Number</option>
                                <option value="Branch Product Sequence Number">Branch Product Sequence Number</option>
                            </select>
                            <jet-input-error :message="form.errors.savings_reference_format" class="mt-2"/>
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
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import FileInput from "@/Jetstream/FileInput.vue";

export default {
    components: {
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetInputError,
        FileInput,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        settings: Object,
        timezones: Object,
        currencies: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                site_online: this.settings.site_online.setting_value,
                timezone: this.settings.timezone.setting_value,
                currency: this.settings.currency.setting_value,
                purchase_code: this.settings.purchase_code.setting_value,
                purchase_code_type: this.settings.purchase_code_type.setting_value,
                allow_self_registration: this.settings.allow_self_registration.setting_value,
                loan_reference_prefix: this.settings.loan_reference_prefix.setting_value,
                loan_reference_format: this.settings.loan_reference_format.setting_value,
                savings_reference_prefix: this.settings.savings_reference_prefix.setting_value,
                savings_reference_format: this.settings.savings_reference_format.setting_value,
            }),
            pageTitle: "System Settings",
            pageDescription: "Manage Settings",
        }
    },
    methods: {
        submit() {
            this.form.post(this.route('settings.system.update'), {})
        },
    },
}
</script>

<style scoped>

</style>
