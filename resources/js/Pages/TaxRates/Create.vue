<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('tax_rates.index')">Tax Rates
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
                                <jet-label for="amount" value="Amount"/>
                                <jet-input id="amount" type="text" class="mt-1 block w-full" v-model="form.amount"
                                           required/>
                                <jet-input-error :message="form.errors.amount" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="type" value="Type"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                    name="type" v-model="form.type" id="type">
                                    <option value="percent">Percentage</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                                <jet-input-error :message="form.errors.gender" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="active">
                                    <div class="flex items-center">
                                        <jet-checkbox name="active" id="active"  v-model:checked="form.active" />
                                        <div class="ml-2">
                                           Active
                                        </div>
                                    </div>
                                </jet-label>
                            </div>
                            <div>
                                <jet-label for="description" value="Description"/>
                                <textarea-input id="description" class="mt-1 block w-full"
                                                v-model="form.description" />
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
                type: 'percent',
                amount: null,
                description: null,
                active: true,
            }),
            pageTitle: "Create Tax Rate",
            pageDescription: "Create Tax Rate",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('tax_rates.store'), {})

        },

    }
}
</script>
<style scoped>

</style>
