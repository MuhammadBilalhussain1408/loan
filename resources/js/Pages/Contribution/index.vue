<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Contribution
            </h2>
        </template>
        <div class=" mx-auto mb-4 flex justify-between items-center">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 hideOnPrint">
                <div>
                    <jet-label for="filter_member_id" value="Member" />
                    <Multiselect v-model="form.member_id" mode="single" :required="true" v-bind="membersMultiSelect" />
                </div>
                <div>
                    <jet-label for="category" value="Member Category" />
                        <select id="category"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                            v-model="form.member_category">
                            <option :value="cat.name" v-for="cat in memberCategories" :key="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-1 gap-2 ">
                    <div>
                        <jet-label for="duration" value="Duration" />
                        <select id="duration"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                            v-model="form.duration">
                            <option value="This Month">This Month</option>
                            <option value="Previous Month">Previous Month</option>
                            <option value="This Year">This Year</option>
                            <option value="Previous Year">Previous Year</option>
                        </select>

                    </div>
                </div>
                <div>
                    <jet-button class="mt-5" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="submit()">
                        Search
                    </jet-button>
                    <jet-button class="mt-5" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="downloadFile()" type="button" v-if="LoanStatement">
                        Download
                    </jet-button>
                    <jet-button class="mt-5" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        type="button" @click="printReport()" v-if="LoanStatement">
                        Print
                    </jet-button>
                </div>
            </div>
            <inertia-link class="btn btn-blue" :href="route('contribution.create')">
                <span>Create </span>
                <span class="hidden md:inline">Contribution</span>
            </inertia-link>
        </div>
        <div class=" mx-auto">
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50">
                        <tr class="text-left font-bold">
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">#</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Surname</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Member Category</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID No</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">15% Employee Contribution</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">30% Employer Contribution</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Total Contribution</th>
                            <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="result in results.data" :key="result.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="border-t">
                                <span class="px-6 py-4 flex items-center">
                                    <inertia-link :href="route('loans.applications.show', result.id)" tabindex="-1"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        {{ result.id }}
                                    </inertia-link>
                                </span>
                            </td>
                            <td class="border-t">
                                <span class="px-6 py-4 flex items-center" v-if="result.Surname">
                                    <inertia-link :href="route('members.show', result.member_id)" tabindex="-1"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        {{ result.Surname }}
                                    </inertia-link>
                                </span>
                            </td>
                            <td class="border-t px-6 py-4 ">
                                {{ result.name }}
                            </td>
                            <td class="border-t px-6 py-4 ">
                                {{ result.member_category }}
                            </td>
                            <td class="border-t px-6 py-4 ">
                                {{ result.id_no }}
                            </td>
                            <td class="border-t px-6 py-4 ">
                                {{ result.contri_15_per.toFixed(2) }}
                            </td>

                            <td class="border-t">
                                {{ result.contri_30_per.toFixed(2) }}
                            </td>
                            <td class="border-t">
                                {{ result.total_contribution }}
                            </td>
                            <td class="border-t w-px pr-2">
                                <div class=" flex gap-4">
                                    <inertia-link :href="route('contribution.show', result.id)" tabindex="-1"
                                        class="text-green-600 hover:text-green-900" title="View">
                                        <font-awesome-icon icon="search" />
                                    </inertia-link>
                                    <inertia-link :href="route('contribution.edit', result.id)" tabindex="-1"
                                        class="text-green-600 hover:text-green-900" title="Edit">
                                        <font-awesome-icon icon="edit" />
                                    </inertia-link>
                                    <a href="#" @click="deleteAction(result.id)" class="text-red-600 hover:text-red-900"
                                        title="Delete">
                                        <font-awesome-icon icon="trash" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="results.data.length === 0">
                            <td class="border-t px-6 py-4 text-center" colspan="9">No records found.</td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <pagination :links="results.links" />
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
const fetchUsers = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type_not_in=member&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return { value: item.id, label: item.name + '(#' + item.id + ')' }
    })
}
const fetchMembers = async (query) => {
    let where = ''

    const response = await fetch(
        route('members.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return { value: item.id, label: item.name + '(#' + item.id + ')' }
    })
}
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Jetstream/Pagination.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetButton from "@/Jetstream/Button.vue";


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
        JetInput,
        JetInputError,
        JetButton
    },
    props: {
        results: Object,
        filters: Object,
        currencies: Object,
        approvalStages: Object,
        purposes: Object,
        products: Object,
        memberCategories:Object

    },
    data() {
        return {
            form: this.$inertia.form({
                // member_type: 'member',
                member_id: null,
                member_category: null,
                end_date: null,
                duration: null
            }),
            // form: {
            //     search: this.filters.search,
            //     status: this.filters.status,
            //     currency_id: this.filters.currency_id,
            //     loan_product_id: this.filters.loan_product_id,
            //     loan_purpose_id: this.filters.loan_purpose_id,
            //     loan_officer_id: this.filters.loan_officer_id,
            //     member_id: this.filters.member_id,
            //     fund_id: this.filters.fund_id,
            //     loan_application_checklist_id: this.filters.loan_application_checklist_id,
            //     current_stage_id: this.filters.current_stage_id,
            //     next_stage_id: this.filters.next_stage_id,
            //     branch_id: this.filters.branch_id,
            //     date_range: this.filters.date_range,
            //     start_date: null,
            //     end_date: null,
            //     duration: null,
            //     processing: false
            // },
            usersMultiSelect: {
                placeholder: 'Search for Loan Officer',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query || this.filters.loan_officer_id)
                }
            },
            membersMultiSelect: {
                placeholder: 'Search for Member',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchMembers(query || this.filters.member_id)
                }
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Loan Applications",
            pageDescription: "Manage Loan Applications",

        }
    },
    // watch: {
    //     form: {
    //         handler: _.debounce(function () {
    //             let query = pickBy(this.form)
    //             this.$inertia.get(this.route('contribution.index', Object.keys(query).length ? query : {}))
    //         }, 1000),
    //         deep: true,
    //     },
    // },
    methods: {
        submit() {
            this.form.get(this.route('contribution.index'), {})
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {
            console.log(this.selectedRecord);
            this.$inertia.delete(this.route('contribution.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped></style>
