<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.provisioning.index')">
                    Loan Provisioning
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Edit
            </h2>
        </template>

        <div class="">
            <div class=" mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-2">
                            <div class="">
                                <jet-label for="name" value="Name"/>
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus
                                           required/>
                                <jet-input-error :message="form.errors.name" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="percentage" value="Percentage"/>
                                <jet-input id="percentage" type="text" class="mt-1 block w-full"
                                           v-model="form.percentage"
                                           required/>
                                <jet-input-error :message="form.errors.percentage" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="lower_limit" value="Lower Limit"/>
                                <jet-input id="lower_limit" type="text" class="mt-1 block w-full"
                                           v-model="form.lower_limit"
                                           required/>
                                <jet-input-error :message="form.errors.lower_limit" class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="upper_limit" value="Upper Limit"/>
                                <jet-input id="upper_limit" type="text" class="mt-1 block w-full"
                                           v-model="form.upper_limit"/>
                                <jet-input-error :message="form.errors.upper_limit" class="mt-2"/>
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
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        provisioning:Object
    },
    components: {
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        TextareaInput,

    },
    data() {
        return {
            form: this.$inertia.form({
                name: this.provisioning.name,
                lower_limit: this.provisioning.lower_limit,
                upper_limit: this.provisioning.upper_limit,
                percentage: this.provisioning.percentage,
                description: this.provisioning.description,
            }),
            pageTitle: "Edit Provisioning",
            pageDescription: "Edit Provisioning",
        }

    },
    methods: {
        submit() {
            this.form.put(this.route('loans.provisioning.update',this.provisioning.id), {})

        },

    }
}
</script>
<style scoped>

</style>
