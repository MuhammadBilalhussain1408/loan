<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('settings.organisation')">
                    Settings
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.collateral_types.index')">
                    Collateral Types
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Create
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-2">
                        <div class="">
                            <jet-label for="name" value="Name"/>
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                       required autofocus/>
                            <jet-input-error :message="form.errors.name" class="mt-2" required/>
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
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {},
    components: {
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
            }),
            pageTitle: "Create Collateral Type",
            pageDescription: "Create Collateral Type",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('loans.collateral_types.store'), {})

        },

    }
}
</script>
<style scoped>

</style>
