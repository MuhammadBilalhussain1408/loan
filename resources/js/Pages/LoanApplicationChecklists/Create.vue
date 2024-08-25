<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('loans.checklists.index')">Application Checklists
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Create
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
                            <div>
                                <jet-label for="description" value="Description"/>
                                <textarea-input id="description" class="mt-1 block w-full"
                                                v-model="form.description"/>
                                <jet-input-error :message="form.errors.description" class="mt-2"/>
                            </div>

                            <jet-primary-button type="button" class="w-28" @click="addItem">
                                Add Item
                            </jet-primary-button>
                            <div class="flex items-center" v-for="(item,index) in form.items">
                                <jet-input id="name" type="text" class="w-64" v-model="item.name"
                                           placeholder="Item name"/>
                                <jet-input id="name" type="text" class="ml-4 w-64" v-model="item.description"
                                           placeholder="Item description"/>
                                <font-awesome-icon icon="trash" class="ml-4 text-red-600 cursor-pointer" @click="form.items.splice(index,1)"/>
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
import JetPrimaryButton from "@/Jetstream/PrimaryButton.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";


export default {
    props: {},
    components: {
        FontAwesomeIcon,
        SelectInput,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,
        JetSecondaryButton,
        JetPrimaryButton,

    },
    data() {
        return {
            form: this.$inertia.form({
                name: null,
                description: null,
                items: [],
            }),
            pageTitle: "Create Checklist",
            pageDescription: "Create Checklist",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('loans.checklists.store'), {})

        },
        addItem() {
            this.form.items.push({id: '', name: '', description: ''})
        }

    }
}
</script>
<style scoped>

</style>
