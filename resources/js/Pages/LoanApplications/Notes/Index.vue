<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.applications.index')">
                    Loan Applications
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Loan Application #{{ application.id }}
                <span class="text-indigo-400 font-medium">/</span>
                Notes
            </h2>
        </template>

        <div class=" mx-auto">
            <loan-application-menu :application="application" :payment-types="paymentTypes"></loan-application-menu>
            <div class="p-4 bg-white">
                <div class="flex justify-end ">
                    <inertia-link class="btn btn-blue" v-if="can('loans.notes.create')"
                                  :href="route('loans.applications.notes.create',application.id)">
                        <span>Add </span>
                        <span class="hidden md:inline">Note</span>
                    </inertia-link>
                </div>
                <article class="p-6 mb-3 text-base bg-white rounded-lg" v-for="item in results.data">
                    <footer class="flex justify-between items-center mb-2">
                        <div class="flex items-center">
                            <p class="inline-flex items-center mr-3 text-sm text-gray-900  font-semibold">
                                <img
                                    class="mr-2 w-6 h-6 rounded-full"
                                    :src="item.created_by.profile_photo_url"
                                    :alt="item.created_by.name">{{ item.created_by.name }}</p>
                            <p class="text-sm text-gray-600 ">
                                <time pubdate :datetime="item.created_at">{{ $filters.time(item.created_at) }}
                                </time>
                            </p>
                        </div>
                    </footer>
                    <p class="text-gray-500">{{ item.description }}</p>
                    <div class="flex items-center mt-4 space-x-4">
                        <inertia-link v-if="can('loans.notes.update')"
                                      :href="route('loans.applications.notes.edit', item.id)"
                                      tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                            <font-awesome-icon icon="edit"/>
                        </inertia-link>
                        <button v-if="can('loans.notes.destroy')" @click="deleteAction(item.id)"
                                class="text-red-600 hover:text-red-900">
                            <font-awesome-icon icon="trash"/>
                        </button>
                    </div>
                </article>
                <pagination v-if="results.data.length" :links="results.links"/>
            </div>
        </div>
        <jet-confirmation-modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete record?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': processing }"
                                   :disabled="processing">
                    Delete Record
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
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
import JetDropdown from '@/Jetstream/Dropdown.vue'
import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import LoanApplicationMenu from "../LoanApplicationMenu.vue";

export default {
    props: {
        application: Object,
        results: Object,
        paymentTypes: Object,
    },
    components: {
        LoanApplicationMenu,
        Pagination,
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
        JetDropdown,
        JetDropdownLink,
        JetDialogModal,
        JetSecondaryButton,
        JetSuccessButton,
        JetConfirmationModal,
        JetDangerButton,

    },
    data() {
        return {
            confirmingDeletion: false,
            selectedRecord: null,
            processing: false,
            action: '',
            pageTitle: "Loan Application Notes",
            pageDescription: "Loan Application Notes",
        }

    },
    mounted() {

    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('loans.applications.notes.destroy', this.selectedRecord), {
                preserveState: false
            })
            this.confirmingDeletion = false
        },
    }
}
</script>
<style scoped>

</style>
