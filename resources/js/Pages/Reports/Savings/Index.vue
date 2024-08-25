<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('reports.index')">
                    Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Savings Reports
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
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-if="can('reports.savings.transactions')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.savings.transaction')">
                                Transactions
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            This report shows individual deposits and withdrawals from savings within the specified time
                            period.
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.savings.transaction')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-if="can('reports.savings.balances')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.savings.balance')">
                                Balances
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            This report shows all deposits and withdrawals from savings within the specified time
                            period.
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.savings.balance')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100"
                        v-if="can('reports.savings.accounts')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.savings.account')">
                                Savings Accounts
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            This report shows all savings balances for savings accounts created in the specified period
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.savings.account')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100"
                        v-if="can('reports.savings.account_statement')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.savings.account_statement')">
                                Savings Account Statement
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            Generates Individual Savings Account Statement
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.savings.account_statement')">
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
