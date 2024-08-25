<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('members.index')">Members
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                {{ member.name }}
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
                <div class="w-full md:w-9/12 md:ml-4 bg-white sm:mt-4">
                    <table class="border-collapse w-full border border-gray-400 bg-white text-sm shadow-sm">
                        <tbody>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Status</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                 <span
                                     class="text-xs font-semibold inline-block py-1 px-2 rounded text-yellow-600 bg-yellow-200 uppercase"
                                     v-if="member.status==='pending'">
                                        Pending
                                 </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 rounded text-yellow-600 bg-yellow-200 uppercase"
                                    v-if="member.status==='inactive'">
                                        In-active
                                </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 rounded text-blue-600 bg-blue-200 uppercase"
                                    v-if="member.status==='archived'">
                                        Archived
                                </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 rounded text-green-600 bg-green-200 uppercase"
                                    v-if="member.status==='active'">
                                        Active
                                    </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 rounded text-red-600 bg-red-200 uppercase"
                                    v-if="member.status==='deceased'">
                                        Deceased
                                </span>
                                <span
                                    class="text-xs font-semibold inline-block py-1 px-2 rounded text-red-600 bg-red-200 uppercase"
                                    v-if="member.status==='deceased'">
                                        Deceased
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Category</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <span>{{ member.category?.name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Designation</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <span>{{ member.designation?.name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Identification
                                Number
                            </td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <span>{{ member.identification_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Graded Tax Number
                            </td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <span>{{ member.graded_tax_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Loan Officer</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <inertia-link class="text-indigo-600" v-if="member.loan_officer"
                                              :href="route('users.show', member.loan_officer_id)"
                                              tabindex="-1">{{ member.loan_officer.name }}
                                </inertia-link>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Number of spouses
                            </td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{
                                    member.number_of_spouses
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Number of
                                children
                            </td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{
                                    member.number_of_children
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Contact Number</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.contact_number }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Home Number</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.home_number }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Date of Birth</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.dob }}
                                ({{ member.age }})
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Email</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.email }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Marital Status</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500 capitalize">
                                {{ member.marital_status }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Monthly/Annual
                                Salary
                            </td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                {{ member.monthly_or_annual_salary }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Date of
                                appointment
                            </td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{
                                    member.date_of_appointment
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Term end date</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.term_end_date }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Profession</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500"><span
                                v-if="member.profession">{{ member.profession.name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Languages</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">
                                <span v-if="member.english" class="mr-2">English</span>
                                <span v-if="member.eswatini" class="mr-2">Eswatini</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Postal Address</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.postal_address }}</td>
                        </tr>


                        <tr v-for="item in member.custom_fields">
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">{{
                                    item.name
                                }}
                            </td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ item.field_value }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Extra Notes</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.description }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Other Loan Instituation</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.other_loan ? member.other_loan.institution : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Other Loan Amount</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.other_loan ? member.other_loan.loan_amount : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="w-1/2 border border-gray-300 font-semibold p-4 text-gray-900">Other Loan Monthly Installment</td>
                            <td class="w-1/2 border border-gray-300 p-4 text-gray-500">{{ member.other_loan ? member.other_loan.monthly_installment : 'N/A' }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
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
import Pagination from '@/Jetstream/Pagination.vue'
import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
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
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        MemberMenu,
    },
    props: {
        member: Object,
        filters: Object,
        roles: Object,

    },
    data() {
        return {

            confirmingMemberDeletion: false,
            showChangeStatusModal: false,
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
            this.confirmingMemberDeletion = true
            this.selectedRecord = id
        },
        destroy() {

            this.$inertia.delete(this.route('members.destroy', this.selectedRecord))
            this.confirmingMemberDeletion = false
        },
    },
}
</script>

<style scoped>

</style>
