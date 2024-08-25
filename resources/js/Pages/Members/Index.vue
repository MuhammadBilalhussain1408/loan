<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Members
            </h2>
        </template>
        <div class=" mx-auto  mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                    <div class="mb-2">
                        <jet-label for="gender" value="Gender"/>
                        <select v-model="form.gender"
                                class="mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option :value="null"/>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
            </filter-search>
            <div>
                <inertia-link class="btn btn-blue" v-if="can('members.create')" :href="route('members.create')">
                    <span>Add </span>
                    <span class="hidden md:inline">Member</span>
                </inertia-link>
                <inertia-link class="btn btn-blue ml-2" v-if="can('members.create')" :href="route('members.import')">
                    <span>Import </span>
                    <span class="hidden md:inline">Members</span>
                </inertia-link>
            </div>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap table-auto">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">System ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID Number</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Sex</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Contact Number</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Age</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="!results.data.length">
                        <td colspan="8" class="px-6 py-4 text-center">
                            No records yet
                        </td>
                    </tr>
                    <tr v-for="result in results.data" :key="result.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center text-indigo-600"
                                          :href="route('members.show', result.id)"
                                          tabindex="-1">
                                {{ result.id }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center  text-indigo-600 focus:text-indigo-500"
                                          :href="route('members.show', result.id)">
                                <img v-if="result.profile_photo_url" class="block w-5 h-5 rounded-full mr-2 -my-2"
                                     :src="result.profile_photo_url">
                                {{ result.name }}
                                <font-awesome-icon icon="trash" v-if="result.deleted_at"
                                                   class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2"/>
                            </inertia-link>
                        </td>

                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', result.id)"
                                          tabindex="-1">
                                {{ result.identification_number }}
                            </inertia-link>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <span class="flex items-center"
                                  tabindex="-1">
                                {{ result.gender }}
                            </span>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', result.id)"
                                          tabindex="-1">
                                {{ result.contact_number }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', result.id)"
                                          tabindex="-1">
                                {{ result.age }}
                            </inertia-link>
                        </td>
                        <td class="border-t">
                            <inertia-link class="px-6 py-4 flex items-center" :href="route('members.show', result.id)"
                                          tabindex="-1">
                                <span v-if="result.status==='pending'" class="bg-yellow-600 text-white rounded px-2">pending</span>
                                <span v-if="result.status==='inactive'" class="bg-red-600 text-white rounded px-2">in-active</span>
                                <span v-if="result.status==='active'" class="bg-green-600 text-white rounded px-2">active</span>
                                <span v-if="result.status==='deceased'" class="bg-red-600 text-white rounded px-2">deceased</span>
                                <span v-if="result.status==='closed'" class="bg-gray-600 rounded px-2">closed</span>
                            </inertia-link>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center gap-4">
                                <inertia-link :href="route('members.show', result.id)"
                                              tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                    <font-awesome-icon icon="search"/>
                                </inertia-link>
                                <inertia-link v-if="can('members.update')"
                                              :href="route('members.edit', result.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <font-awesome-icon icon="edit"/>
                                </inertia-link>
                                <a href="#" v-if="can('members.destroy')" @click="deleteAction(result.id)"
                                   class="text-red-600 hover:text-red-900" title="Delete">
                                    <font-awesome-icon icon="trash"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="results.links"/>
        </div>
        <jet-confirmation-modal :show="confirmDeletion" @close="confirmDeletion = false">
            <template #title>
                Delete Account
            </template>

            <template #content>
                Are you sure you want to delete record?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
                    Delete Record
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
        <jet-dialog-modal :show="showImportMembersModal" @close="showImportMembersModal = false">
            <template #title>
                Import Members
            </template>

            <template #content>

                <div class="mt-4">
                    <jet-label for="file" value="File(xlsx)"/>
                    <file-input v-model="file" class="block w-full" id="file" type="file"/>

                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="showImportMembersModal = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="importMembers" :class="{ 'opacity-25': processing }" :disabled="processing">
                    Import
                </jet-button>
            </template>
        </jet-dialog-modal>
        <teleport to="head">
            <title>{{ pageTitle }}</title>
            <meta property="og:description" :content="pageDescription">
        </teleport>
    </app-layout>

</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import JetInput from "../../Jetstream/Input.vue";
import JetInputError from "../../Jetstream/InputError.vue";
import JetDialogModal from "../../Jetstream/DialogModal.vue";
import JetButton from "../../Jetstream/Button.vue";
import FileInput from "../../Jetstream/FileInput.vue";

export default {
    components: {
        FileInput,
        JetButton, JetDialogModal, JetInputError, JetInput,
        FontAwesomeIcon,
        AppLayout,
        Pagination,
        FilterSearch,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        results: Object,
        filters: Object,
        branches: Object,
        types: Object,
        countries: Object,

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                status: this.filters.status,
                gender: this.filters.gender,
                branch_id: this.filters.branch_id,
                processing: false
            },
            processing: false,
            file: null,
            confirmDeletion: false,
            showImportMembersModal: false,
            selectedRecord: null,
            pageTitle: "Members",
            pageDescription: "Manage Members",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('members.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
    },
    methods: {
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmDeletion = true
            this.selectedRecord = id
        },
        importMembers(){
            const formData = new FormData();
            formData.append("file",this.file);
            axios.post(route('members.import'), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.processing = false
                this.showImportMembersModal = false
                this.$inertia.reload()
            }).catch(error => {
                this.processing = false
            });
        },
        destroy() {

            this.$inertia.delete(this.route('members.destroy', this.selectedRecord))
            this.confirmDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
