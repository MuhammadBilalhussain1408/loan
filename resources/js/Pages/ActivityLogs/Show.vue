<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('activity_logs.index')">
                    Activity Logs
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Details
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <table class="w-full whitespace-no-wrap">
                    <tbody>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4 ">
                            <span class="flex items-center">
                                Causer
                            </span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link :href="route('users.show',activity.causer_id)" class="text-indigo-600"
                                          v-if="activity.causer">
                                {{ activity.causer.name }}
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4 ">
                            <span class="flex items-center">
                                Subject
                            </span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <span>
                                {{ activity.subject_type }} (#{{activity.subject_id}})
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4 ">
                            <span class="flex items-center">
                                Description
                            </span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <span>
                                {{ activity.description }}
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t px-6 py-4 ">
                            <span class="flex items-center">
                                Date
                            </span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <span>
                                {{ $filters.time(activity.created_at) }}
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-if="activity.properties.attributes" class="mt-4">
                    <h4>Changes</h4>
                    <table class="w-full whitespace-no-wrap">
                        <thead class="bg-gray-50">
                        <tr class="text-left font-bold">
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Attribute</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Old</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">New</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in changes "
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t px-6 py-4 ">
                            <span class="flex items-center">
                                {{ item.attribute }}
                            </span>
                            </td>
                            <td class="border-t px-6 py-4 ">
                                {{ item.old }}
                            </td>
                            <td class="border-t px-6 py-4 ">
                                {{ item.new }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
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
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        activity: Object
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
            changes: {},
            pageTitle: "Activity Logs",
            pageDescription: "Activity Logs",
        }

    },
    mounted() {
        if (this.activity.properties.attributes) {
            Object.keys(this.activity.properties.attributes).forEach(key => {
                let item = this.activity.properties.attributes[key]
                this.changes[key] = {
                    attribute: key,
                    old: '',
                    new: item,
                }
            })
            Object.keys(this.activity.properties.old).forEach(key => {
                this.changes[key].old = this.activity.properties.old[key]
            })
        }
    },
    methods: {}
}
</script>
<style scoped>

</style>
