<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reports
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="border bg-white">
                <inertia-link v-if="can('reports.accounting.index')"
                              class="border-t-2 border-gray-100 font-medium text-indigo-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('reports.accounting.index')">
                    Accounting Reports
                </inertia-link>
                <inertia-link v-if="can('reports.loans.index')"
                              class="border-t-2 border-gray-100 font-medium text-indigo-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('reports.loans.index')">
                    Loan Reports
                </inertia-link>
                <inertia-link v-if="can('reports.savings.index')"
                              class="border-t-2 border-gray-100 font-medium text-indigo-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('reports.savings.index')">
                    Savings Reports
                </inertia-link>
                <inertia-link v-if="can('reports.users.index')"
                              class="border-t-2 border-gray-100 font-medium text-indigo-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('reports.users.index')">
                    Staff Reports
                </inertia-link>
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

export default {
    components: {
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
