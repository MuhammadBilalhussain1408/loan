<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('loans.repayments.index')">
                    Loan Repayments
                </inertia-link>

                <span class="text-indigo-400 font-medium">/</span> Add Bulk Repayments
            </h2>
        </template>
        <div class=" mx-auto">
            <a :href="sampleUrl" class="text-indigo-600">Download Sample</a>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 gap-2">
                        <div class="">
                            <jet-label for="file" value="File"/>
                            <file-input id="file" class="mt-1 block w-full" v-model="form.file"
                                       required/>
                            <jet-input-error :message="form.errors.file" class="mt-2"/>
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
    props: {
        sampleUrl:String
    },
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
                file: null,
            }),
            pageTitle: "Add Bulk Repayments",
            pageDescription: "Add Bulk Repayments",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('loans.repayments.store_bulk_repayments'), {})

        },

    }
}
</script>
<style scoped>

</style>
