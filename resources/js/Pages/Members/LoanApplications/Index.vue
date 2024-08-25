<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('members.index')">Members
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> {{ member.name }}
            </h2>
        </template>
        <div class="mx-auto">
            <div class="md:flex md:items-start">
                <div class="bg-white relative shadow-xl mb-4 mt-20 w-full md:w-3/12">
                    <div class="col-span-12 lg:col-span-4 xxl:col-span-3 flex lg:block flex-col-reverse">
                        <div class="intro-y box mt-5 lg:mt-0">
                            <member-menu :member="member"></member-menu>
                        </div>
                    </div>

                </div>
                <div class="w-full md:w-9/12 p-4 md:ml-4 bg-white sm:mt-4">
                    <div class="flex justify-between ">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Loan Applications</h2>
                        <inertia-link class="btn btn-blue" v-if="can('loans.applications.create')"
                                      :href="route('loans.applications.create',{member_id:member.id})">
                            <span>Create </span>
                            <span class="hidden md:inline">Application</span>
                        </inertia-link>
                    </div>
                    <div class="mt-4 relative overflow-x-auto">
                        <table class="w-full whitespace-no-wrap">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Product</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Applied Amount</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Current Stage</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Status</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="result in results.data" :key="result.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t">
                            <span class="px-6 py-4 flex items-center">
                                 <inertia-link :href="route('loans.show', result.id)"
                                               tabindex="-1" class="text-indigo-600 hover:text-indigo-900">
                                    {{ result.id }}
                                </inertia-link>
                            </span>
                                </td>
                                <td class="border-t px-6 py-4 ">
                           <span class="flex items-center" v-if="result.product">
                                {{ result.product.name }}
                            </span>
                                </td>
                                <td class="border-t px-6 py-4 ">
                            <span v-if="result.approved_amount>0 && result.approved_amount!=result.applied_amount"
                                  class="mr-2">{{ $filters.formatNumber(result.approved_amount) }}</span>
                                    <span
                                        :class="result.approved_amount>0 && result.approved_amount!=result.applied_amount?'line-through':''">
                                {{ $filters.formatNumber(result.applied_amount) }}
                            </span>
                                </td>

                                <td class="border-t">
                           <span class="px-6 py-4 flex items-center" v-if="result.current_stage">
                                {{ result.current_stage.stage?.name }}
                            </span>
                                </td>


                                <td class="border-t px-6 py-4 ">
                           <span class="px-2 bg-orange-600 text-white rounded text-sm"
                                 v-if="result.status==='pending'||result.status==='submitted'">
                                pending
                            </span>
                                    <span class="px-2 bg-green-600 text-white rounded text-sm"
                                          v-if="result.status==='approved'">
                                approved
                            </span>
                                    <span class="px-2 bg-red-600 text-white rounded text-sm"
                                          v-if="result.status==='rejected'">
                                rejected
                            </span>
                                </td>
                                <td class="border-t w-px pr-2">
                                    <div class=" flex items-center gap-4">
                                        <inertia-link :href="route('loans.applications.show', result.id)"
                                                      tabindex="-1" class="text-green-600 hover:text-green-900" title="View">
                                            <font-awesome-icon icon="search"/>
                                        </inertia-link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="results.data.length === 0">
                                <td class="border-t px-6 py-4" colspan="10">No records found.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <pagination v-if="results.data.length" :links="results.links"/>
                </div>
            </div>
        </div>
        <jet-confirmation-modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <template #title>
                Delete Record
            </template>

            <template #content>
                Are you sure you want to delete this record?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="confirmingDeletion = false">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': form.processing }"
                                   :disabled="form.processing">
                    Delete
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
import Pagination from '@/Jetstream/Pagination.vue'
import FilterSearch from '@/Jetstream/FilterSearch.vue'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import MemberMenu from '@/Pages/Members/MemberMenu.vue'

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
        MemberMenu,
    },
    props: {
        member: Object,
        results: Object,

    },
    data() {
        return {
            form: {
                processing: false
            },
            confirmingDeletion: false,
            selectedRecord: null,
            pageTitle: "Loan Applications",
            pageDescription: "Loan Applications",

        }
    },
    methods: {
        deleteAction(id) {
            this.confirmingDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('loans.applications.destroy', this.selectedRecord))
            this.confirmingDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
