<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Custom Fields
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                </div>
            </filter-search>
            <inertia-link v-if="can('custom_fields.create')" class="btn btn-blue"
                          :href="route('custom_fields.create')">
                <span>Create </span>
                <span class="hidden md:inline">Field</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Category</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Type</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Required</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Active</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="result in results.data" :key="result.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center">
                                {{ result.name }}
                            </span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <span v-if="result.category==='client'">Clients</span>
                            <span v-if="result.category==='loan'">Loans</span>
                            <span v-if="result.category==='repayment'">Repayments</span>
                            <span v-if="result.category==='savings'">Savings</span>
                            <span v-if="result.category==='collateral'">Collateral</span>
                            <span v-if="result.category==='user'">Users</span>
                            <span v-if="result.category==='branch'">Branches</span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <span v-if="result.type==='text'">Text</span>
                            <span v-if="result.type==='select'">Select</span>
                            <span v-if="result.type==='dropdown'">Dropdown</span>
                            <span v-if="result.type==='number'">Number</span>
                            <span v-if="result.type==='date'">Date</span>
                            <span v-if="result.type==='checkbox'">Checkbox</span>
                            <span v-if="result.type==='textarea'">Textarea</span>
                            <span v-if="result.type==='radio'">Radio</span>
                            <span v-if="result.type==='select_db'">Select from database</span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <span v-if="result.required">yes</span>
                            <span v-else>no</span>
                        </td>
                        <td class="border-t px-6 py-4 ">
                            <span v-if="result.active" class="bg-green-600 px-2 text-white">yes</span>
                            <span v-else class="bg-red-600 px-2 text-white">no</span>
                        </td>

                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center space-x-2">

                                <inertia-link v-if="can('custom_fields.update')"
                                              :href="route('custom_fields.edit', result.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </inertia-link>
                                <a href="#" v-if="can('custom_fields.destroy')"
                                   @click="deleteAction(result.id)"
                                   class="text-red-600 hover:text-red-900">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="results.data.length === 0">
                        <td class="border-t px-6 py-4 text-center" colspan="6">No records found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="results.links"/>
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

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
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
import Icon from '@/Jetstream/Icon.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import SearchFilter from '@/Jetstream/SearchFilter.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'

export default {
    components: {
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

    },
    data() {
        return {
            form: {
                search: this.filters.search,
                type: this.filters.type,
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Custom Fields",
            pageDescription: "Manage Custom Fields",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('custom_fields.index', Object.keys(query).length ? query : {}))
            }, 500),
            deep: true,
        },
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

            this.$inertia.delete(this.route('custom_fields.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
