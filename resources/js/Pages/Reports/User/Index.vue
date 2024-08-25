<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('reports.index')">
                    Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Staff Reports
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="border bg-white overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Description</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-if="can('reports.users.performance')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.users.performance')">
                                Staff Performance Report
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            Shows Staff Performance Report
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.users.performance')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    </tbody>
                </table>
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
import mapValues from 'lodash/mapValues'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    components: {
        FontAwesomeIcon,
        AppLayout,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        reports: Object,
    },
    data() {
        return {
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Reports",
            pageDescription: "Manage Reports",
        }
    },

    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('tariffs.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
