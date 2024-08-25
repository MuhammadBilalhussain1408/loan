<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('portal.loans.index')">
                    Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Loan #{{ loan.id }}
                <span class="text-indigo-400 font-medium">/</span>
                Files
            </h2>
        </template>

        <div class=" mx-auto">
            <loan-menu :loan="loan" :payment-types="paymentTypes"></loan-menu>
            <div class="p-4 bg-white">
                <div class="flex justify-end ">
                    <inertia-link class="btn btn-blue" v-if="can('loans.files.create')"
                                  :href="route('portal.loans.files.create',loan.id)">
                        <span>Add </span>
                        <span class="hidden md:inline">File</span>
                    </inertia-link>
                </div>
                <div class="mt-4 relative overflow-x-auto">
                    <table class="w-full whitespace-no-wrap table-auto">
                        <thead class="bg-gray-50">
                        <tr class="text-left font-bold">
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Size</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Description</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Date</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="!files.data.length">
                            <td colspan="5" class="px-6 py-4 text-center">
                                No Files Yet
                            </td>
                        </tr>
                        <tr v-for="file in files.data" :key="file.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t">
                                <a :href="route('files.download', file.id)" target="_blank"
                                   tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    <span class="px-6 py-4 flex items-center">
                                    {{ file.name }}
                                    </span>
                                </a>
                            </td>
                            <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                    {{ file.file_size_in_units }}
                                    </span>
                            </td>
                            <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                    {{ file.description }}
                                    </span>
                            </td>
                            <td class="border-t">
                                    <span class="px-6 py-4 flex items-center">
                                    {{ file.time_ago }}
                                    </span>
                            </td>
                            <td class="border-t w-px pr-2">
                                <div class=" flex items-center space-x-2">
                                    <a :href="route('files.download', file.id)" title="Click to download"
                                       target="_blank"
                                       tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                        <font-awesome-icon icon="download"/>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="files.data.length" :links="files.links"/>
            </div>
        </div>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>
</template>
<script>
import AppLayout from '@/Pages/MemberPortal/Layouts/AppLayout.vue'
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
import LoanMenu from '../LoanMenu.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    props: {
        loan: Object,
        files: Object,
        paymentTypes: Object,
    },
    components: {
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
        LoanMenu,

    },
    data() {
        return {
            confirmingDeletion: false,
            selectedRecord: null,
            processing: false,
            action: '',
            pageTitle: "Loan Files",
            pageDescription: "Loan Files",
        }

    },
    mounted() {

    },
    methods: {

    }
}
</script>
<style scoped>

</style>
