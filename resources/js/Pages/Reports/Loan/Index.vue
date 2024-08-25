<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('reports.index')">
                    Reports
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Loan Reports
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
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-if="can('reports.loans.collection_sheet')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.collection_sheet')">
                                Collection Sheet
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            See all repayments due on a particular day. Designed for loan officers to use in the field
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.collection_sheet')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-if="can('reports.loans.repayments')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.repayment')">
                                Repayments
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            This report shows the repayment made between start date and end date
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.repayment')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100"
                        v-if="can('reports.loans.expected_repayments')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.expected_repayment')">
                                Expected Repayments
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            This report shows the expected repayments vs actual repayments within the specified time
                            period
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.expected_repayment')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-if="can('reports.loans.arrears')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.arrears')">
                                Arrears
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            This report lists all loans in arrears
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.arrears')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100" v-if="can('reports.loans.disbursement')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.disbursement')">
                                Disbursement Report
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            Returns all loans disbursed within a certain time period with their amounts and basic
                            information
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.disbursement')">
                                <font-awesome-icon icon="search"/>
                            </inertia-link>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-100 focus-within:bg-gray-100 hidden"
                        v-if="can('reports.loans.portfolio_at_risk')">
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.portfolio_at_risk')">
                                Portfolio at risk
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            Gives an overview of the PAR per branch broken down in bands.
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <inertia-link
                                class="text-indigo-600"
                                :href="route('reports.loans.portfolio_at_risk')">
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
