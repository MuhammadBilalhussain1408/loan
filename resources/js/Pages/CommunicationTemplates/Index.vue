<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Communication Templates
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">
            <filter-search v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
                <div class="w-80 mt-2 px-4 py-6 shadow-xl bg-white rounded">
                </div>
            </filter-search>
            <inertia-link v-if="can('communication.templates.create')" class="btn btn-blue"
                          :href="route('communication.templates.create')">
                <span>Create </span>
                <span class="hidden md:inline">Template</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                    <tr class="text-left font-bold">
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Type</th>
                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="template in templates.data" :key="template.id"
                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                    {{ template.id }}
                            </span>
                        </td>
                        <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                    {{ template.name }}
                            </span>
                        </td>
                        <td class="border-t">
                             <span class="px-6 py-4 flex items-center" v-if="template.type==='email'">
                                Email
                            </span>
                            <span class="px-6 py-4 flex items-center" v-if="template.type==='sms'">
                                SMS
                            </span>
                        </td>
                        <td class="border-t w-px pr-2">
                            <div class=" flex items-center space-x-2">
                                <inertia-link v-if="can('communication.templates.update')"
                                              :href="route('communication.templates.edit', template.id)"
                                              tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    Edit
                                </inertia-link>
                                <a href="#" v-if="can('communication.templates.destroy') && !template.is_system"
                                   @click="deleteAction(template.id)"
                                   class="text-red-600 hover:text-red-900">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="templates.data.length === 0">
                        <td class="border-t px-6 py-4 text-center" colspan="4">No templates found.</td>
                    </tr>
                    </tbody>
                </table>

            </div>
            <pagination v-if="templates.data.length" :links="templates.links"/>
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
        Icon,
        Pagination,
        SearchFilter,
        FilterSearch,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        templates: Object,
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
            pageTitle: "Communication Templates",
            pageDescription: "Communication Templates",

        }
    },
    watch: {
        form: {
            handler: _.debounce(function () {
                let query = pickBy(this.form)
                this.$inertia.get(this.route('communication.templates.index', Object.keys(query).length ? query : {}))
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

            this.$inertia.delete(this.route('communication.templates.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
