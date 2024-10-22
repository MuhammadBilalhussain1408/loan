<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Settings
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <inertia-link v-if="can('settings')"
                    class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                    :href="route('settings.organisation')">
                    <font-awesome-icon icon="building" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Organisation Settings
                </inertia-link>
                <inertia-link v-if="can('subscription')"
                              class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                              :href="route('settings.subscriptions.index')">
                    <font-awesome-icon icon="money-bill" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Subscription
                </inertia-link>
                <inertia-link v-if="can('settings.general')"
                    class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                    :href="route('settings.general')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    General Settings
                </inertia-link>
                <inertia-link v-if="can('settings.system')"
                    class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                    :href="route('settings.system')">
                    <font-awesome-icon icon="cogs" class="w-4 h-4 mr-2"></font-awesome-icon>
                    System Settings
                </inertia-link>
                <inertia-link v-if="can('settings.email')"
                    class="hidden border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                    :href="route('settings.email')">
                    <font-awesome-icon icon="envelope" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Email Settings
                </inertia-link>
                <inertia-link v-if="can('settings.sms')"
                    class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                    :href="route('settings.sms')">
                    <font-awesome-icon icon="sms" class="w-4 h-4 mr-2"></font-awesome-icon>
                    SMS Settings
                </inertia-link>
                <inertia-link v-if="can('settings.other')"
                    class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                    :href="route('settings.other')">
                    <font-awesome-icon icon="wrench" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Other Settings
                </inertia-link>
                <inertia-link v-if="can('settings.other')"
                    class="w-full border-t-2 border-gray-100 font-medium text-gray-600 py-2 px-4 w-full block hover:bg-gray-100 transition duration-150"
                    :href="route('users.roles.index')">
                    <font-awesome-icon icon="wrench" class="w-4 h-4 mr-2"></font-awesome-icon>
                    Role Setting
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
    metaInfo: {title: 'Tariffs'},
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
            pageTitle: "Settings",
            pageDescription: "Manage Settings",
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
