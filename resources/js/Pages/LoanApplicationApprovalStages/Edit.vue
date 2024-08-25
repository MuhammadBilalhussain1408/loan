<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('loans.approval_stages.index')">Approval Stages
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
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                           required autofocus/>
                                <jet-input-error :message="form.errors.name" class="mt-2"/>
                            </div>

                            <div class="">
                                <jet-label for="field_position" value="Field Position"/>
                                <jet-input id="field_position" type="number" class="mt-1 block w-full"
                                           v-model="form.field_position"/>
                                <jet-input-error :message="form.errors.field_position" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="is_last">
                                    <div class="flex items-center">
                                        <jet-checkbox name="active" id="is_last" v-model:checked="form.is_last"/>
                                        <div class="ml-2">
                                            Is Last
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
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        stage: Object,
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
                name: this.stage.name,
                assigned_to_id: this.stage.assigned_to_id,
                field_position: this.stage.field_position,
                description: this.stage.description,
                is_last: this.stage.is_last,
            }),
            pageTitle: "Edit Stage",
            pageDescription: "Edit Stage",
        }

    },
    methods: {
        submit() {
            this.form.put(this.route('loans.approval_stages.update', this.stage.id), {})

        },

    }
}
</script>
<style scoped>

</style>
