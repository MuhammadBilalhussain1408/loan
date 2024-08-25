<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.show',loan.id)">Loan
                    #{{ loan.id }}
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Add Collateral
            </h2>
        </template>

        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <jet-label for="loan_collateral_type_id" value="Type"/>
                            <select-input id="loan_collateral_type_id" class="mt-1 block w-full"
                                          v-model="form.loan_collateral_type_id" required>
                                <option v-for="item in types" :value="item.id">{{ item.name }}</option>
                            </select-input>
                            <jet-input-error :message="form.errors.loan_collateral_type_id" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="value" value="Value"/>
                            <jet-input id="value" type="number" class="mt-1 block w-full"
                                       v-model="form.value" required/>
                            <jet-input-error :message="form.errors.value" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="file" value="File"/>
                            <file-input v-model="form.file" class="mt-1 block w-full" id="file" type="file"/>
                            <jet-input-error :message="form.errors.file" class="mt-2"/>
                        </div>
                        <div v-for="(field,index) in form.custom_fields" class="">
                            <div v-if="field.type==='text'">
                                <jet-label :for="'field_'+field.id" :value="field.name"/>
                                <jet-input :id="'field_'+field.id" type="text" class="mt-1 block w-full"
                                           v-model="field.field_value"
                                           :required="field.required"/>

                            </div>
                            <div v-if="field.type==='number'">
                                <jet-label :for="'field_'+field.id" :value="field.name"/>
                                <jet-input :id="'field_'+field.id" type="number" class="mt-1 block w-full"
                                           v-model="field.field_value"
                                           :required="field.required"/>

                            </div>
                            <div v-if="field.type==='textarea'">
                                <jet-label :for="'field_'+field.id" :value="field.name"/>
                                <textarea-input :id="'field_'+field.id" type="number" class="mt-1 block w-full"
                                                v-model="field.field_value"
                                                :required="field.required"/>
                            </div>
                            <div v-if="field.type==='dropdown'">
                                <jet-label :for="'field_'+field.id" :value="field.name"/>
                                <select-input :id="'field_'+field.id" class="mt-1 block w-full"
                                              v-model="field.field_value"
                                              :required="field.required">
                                    <option v-for="option in field.options">{{ option }}</option>
                                </select-input>
                            </div>
                            <div v-if="field.type==='file'">
                                <jet-label :for="'field_'+field.id">
                                    {{ field.name }}
                                    <span v-if="field.file" class="ml-2">
                                            <a target="_blank" class="text-indigo-400"
                                               :href="route('files.download',field.file.id)">({{ field.file.name }})</a>
                                        </span>
                                </jet-label>
                                <file-input :id="'field_'+field.id" type="number" class="mt-1 block w-full"
                                            v-model="field.field_value"
                                            :required="field.required"/>
                            </div>
                            <div v-if="field.type==='checkbox'">
                                <jet-label :for="'field_'+field.id">
                                    <div class="flex items-center">
                                        <jet-checkbox :id="'field_'+field.id" v-model:checked="field.field_value"/>
                                        <div class="ml-2">
                                            {{ field.name }}
                                        </div>
                                    </div>
                                </jet-label>

                            </div>
                            <div v-if="field.type==='radio'">
                                <h4>{{ field.name }}</h4>
                                <div class="flex items-center mb-4" v-for="option in field.options">
                                    <input :id="'field_option_'+option" type="radio" :name="field.name"
                                           :value="option" v-model="field.field_value"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label :for="'field_option_'+option"
                                           class="ml-2 text-sm font-medium ">{{
                                            option
                                        }}</label>
                                </div>
                            </div>
                            <div v-if="field.type==='checkboxes'">
                                <div v-for="option in field.options" class="grid grid-cols-1 gap-2">
                                    <jet-label :for="'field_option_'+option">
                                        <div class="flex items-center">
                                            <jet-checkbox :id="'field_option_'+option" :value="option"
                                                          v-model:checked="field.field_value"/>
                                            <div class="ml-2">
                                                {{ option }}
                                            </div>
                                        </div>
                                    </jet-label>
                                </div>
                            </div>
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
        types: Object,
        customFields: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                loan_collateral_type_id: null,
                value: null,
                status: null,
                description: null,
                file: null,
                custom_fields: this.customFields,
            }),
            pageTitle: "Create Loan Collateral",
            pageDescription: "Create Loan Collateral",

        }
    },

    methods: {
        submit() {
            this.form.post(this.route('loans.collaterals.store', this.loan.id), {})
        },
    },
}
</script>

<style scoped>

</style>
