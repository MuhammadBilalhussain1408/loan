<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('custom_fields.index')">
                    Custom Fields
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
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
                                <jet-input-error :message="form.errors.name" class="mt-2" required/>
                            </div>
                            <div>
                                <jet-label for="category" value="Category"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                    name="type" v-model="form.category" id="category" required>
                                    <option value="loan">Loans</option>
                                    <option value="client">Clients</option>
                                    <option value="repayment">Repayments</option>
                                    <option value="savings">Savings</option>
                                    <option value="collateral">Collateral</option>
                                    <option value="user">User</option>
                                    <option value="branch">Branch</option>
                                </select>
                                <jet-input-error :message="form.errors.type" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="type" value="Type"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                    name="type" v-model="form.type" id="type" required>
                                    <option value="text">Text</option>
                                    <option value="number">Number</option>
                                    <option value="dropdown">Dropdown</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="file">File</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="checkboxes">Checkboxes</option>
                                    <option value="radio">Radio</option>
                                </select>
                                <jet-input-error :message="form.errors.type" class="mt-2"/>
                            </div>
                            <div v-if="form.type==='dropdown'|| form.type==='radio' || form.type==='checkboxes'"
                                 class="grid grid-cols-1 gap-2 bg-gray-200 p-4">
                                <jet-secondary-button @click.native="addOption(form.id)">
                                    Add Option
                                </jet-secondary-button>
                                <div v-for="(item,index) in form.options" class="grid grid-cols-2 gap-2">
                                    <div class="">
                                        <jet-label :for="'edit_option_'+index" :value="'Option '+(index+1)"/>
                                        <jet-input :id="'edit_option_'+index" type="text" class="mt-1 block w-full"
                                                   v-model="form.options[index]"
                                                   required/>
                                        <jet-input-error :message="form.errors.options" class="mt-2"/>
                                    </div>
                                    <div>
                                        <font-awesome-icon icon="trash" v-on:click="form.options.splice(index,1)"
                                                           class="w-4 h-4 mr-2 mt-8"></font-awesome-icon>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden">
                                <jet-label for="field_position" value="Position"/>
                                <jet-input id="field_position" type="text" class="mt-1 block w-full"
                                           v-model="form.field_position"/>
                                <jet-input-error :message="form.errors.field_position" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="default_values" value="Default Value"/>
                                <jet-input id="default_values" type="text" class="mt-1 block w-full"
                                           v-model="form.default_values"/>
                                <jet-input-error :message="form.errors.default_values" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="required">
                                    <div class="flex items-center">
                                        <jet-checkbox name="required" id="required"
                                                      v-model:checked="form.required"/>
                                        <div class="ml-2">
                                            Required
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                            <div>
                                <jet-label for="active">
                                    <div class="flex items-center">
                                        <jet-checkbox name="active" id="active"
                                                      v-model:checked="form.active"/>
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
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        field: Object
    },
    components: {
        JetSecondaryButton,
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
                name: this.field.name,
                type: this.field.type,
                category:this.field.category,
                options: this.field.options,
                field_position: this.field.field_position,
                rules:this.field.rules,
                classes: this.field.classes,
                default_values: this.field.default_values,
                description: this.field.description,
                active: this.field.active,
                required: this.field.required,
            }),

            pageTitle: "Edit Field",
            pageDescription: "Edit Field",
        }

    },
    methods: {
        submit() {
            this.form.put(this.route('custom_fields.update', this.field.id), {})

        },

    },
    watch: {}
}
</script>
<style scoped>

</style>
