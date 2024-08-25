<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Organisation Settings
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">

                <inertia-link v-if="can('custom_fields.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('custom_fields.index')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Custom Fields
                </inertia-link>
                <inertia-link v-if="can('loans.checklists.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('loans.checklists.index')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Loan Application Checklists
                </inertia-link>
                <inertia-link v-if="can('loans.approval_stages.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('loans.approval_stages.index')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Loan Application Approval Stages
                </inertia-link>
                <inertia-link v-if="can('files.types.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('files.types.index')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage File Types
                </inertia-link>
                <inertia-link v-if="can('members.member_relationships.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('members.relationships.index')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Member Relationships
                </inertia-link>
                <inertia-link v-if="can('members.identification_types.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('members.identification_types.index')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Client Identification Types
                </inertia-link>
                <inertia-link v-if="can('members.titles.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('members.titles.index')">
                    <font-awesome-icon icon="user-tie" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Titles
                </inertia-link>
                <inertia-link v-if="can('members.professions.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('members.professions.index')">
                    <font-awesome-icon icon="user-tie" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Professions
                </inertia-link>
                <inertia-link v-if="can('members.categories.index')"
                              class=" border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('members.categories.index')">
                    <font-awesome-icon icon="user-tie" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Member Categories
                </inertia-link>
                <inertia-link v-if="can('members.designations.index')"
                              class="border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('members.designations.index')">
                    <font-awesome-icon icon="user-tie" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Member Designations
                </inertia-link>
                <inertia-link v-if="can('payment_types')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('payment_types.index')">
                    <font-awesome-icon icon="money-bill" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Payment Methods
                </inertia-link>
                <inertia-link v-if="can('loans.funds.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('loans.funds.index')">
                    <font-awesome-icon icon="wallet" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Funds
                </inertia-link>

                <inertia-link v-if="can('loans.collateral_types.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('loans.collateral_types.index')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Collateral Types
                </inertia-link>
                <inertia-link v-if="can('loans.purposes.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('loans.purposes.index')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Loan Purposes
                </inertia-link>
                <inertia-link v-if="can('tax_rates')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('tax_rates.index')">
                    <font-awesome-icon icon="dollar-sign" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Tax Rates
                </inertia-link>
                <inertia-link v-if="can('currencies')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('currencies.index')">
                    <font-awesome-icon icon="dollar-sign" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Currencies
                </inertia-link>
                <inertia-link v-if="can('branches')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('branches.index')">
                    <font-awesome-icon icon="building" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Branches
                </inertia-link>
                <inertia-link v-if="can('users.roles.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('users.roles.index')">
                    <font-awesome-icon icon="users" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage Roles
                </inertia-link>
                <inertia-link v-if="can('communication.sms_gateways.index')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('communication.sms_gateways.index')">
                    <font-awesome-icon icon="envelope" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Manage SMS Gateways
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
    props: {},
    data() {
        return {
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Organisation Settings",
            pageDescription: "Manage Settings",
        }
    },

    methods: {},
}
</script>

<style scoped>

</style>
