<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
            </div>
        </template>
        <div class="mx-auto">
            <h4 class="mt-4 font-bold">Member Count By Designations</h4>
            <div class="grid sm:grid-cols-1 md:grid-cols-6 mt-2  gap-2">
                <div class="" v-for="item in designations">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Total Members </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            item.total_members
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="users"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4"><span
                                class="whitespace-no-wrap">{{ item.name }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="mt-4 font-bold">Member Count By Categories</h4>
            <div class="grid sm:grid-cols-1 md:grid-cols-6 mt-2  gap-2">
                <div class="" v-for="item in categories">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Total Members </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            item.total_members
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="users"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4"><span
                                class="whitespace-no-wrap">{{ item.name }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid sm:grid-cols-1 md:grid-cols-5 mt-4 gap-2">
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Total Members </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalMembers
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="users"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4"><span
                                class="whitespace-no-wrap">Total members</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Total Loan Applications</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ totalLoanApplications }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">All loan applications</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Loan Applications Pending</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ totalLoansPending }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-yellow-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan applications pending</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Loan Applications Approved </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ totalLoansApproved }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                Total loan applications approved
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Loan Applications Rejected </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ totalLoansRejected }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                Total loan applications rejected
                            </p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="grid sm:grid-cols-1 md:grid-cols-3 mt-4  gap-2">
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Loan Principal </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanPrincipal
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4"><span
                                class="whitespace-no-wrap">Total loan principal disbursed</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Principal Repaid</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanPrincipalRepaid
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan principal repaid</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Principal Balance</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanPrincipalOutstanding
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="minus"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan principal balance</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid sm:grid-cols-1 md:grid-cols-3 mt-4  gap-2">
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Loan Interest </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanInterest
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4"><span
                                class="whitespace-no-wrap">Total loan interest </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Interest Repaid</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanInterestRepaid
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan interest repaid</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Interest Balance</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanInterestOutstanding
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="minus"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan interest balance</span>
                            </p>
                        </div>
                    </div>
                </div>


            </div>
            <div class="grid sm:grid-cols-1 md:grid-cols-3 mt-4  gap-2">
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Loan Fees </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanFees
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4"><span
                                class="whitespace-no-wrap">Total loan fees </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Fees Repaid</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanFeesRepaid
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan fees repaid</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Fees Balance</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanFeesOutstanding
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="minus"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan fees balance</span>
                            </p>
                        </div>
                    </div>
                </div>


            </div>
            <div class="grid sm:grid-cols-1 md:grid-cols-3 mt-4  gap-2">
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Loan Penalties </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanPenalties
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4"><span
                                class="whitespace-no-wrap">Total loan penalties </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Penalties Repaid</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanPenaltiesRepaid
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan penalties repaid</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Penalties Balance</h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanPenaltiesOutstanding
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="minus"/>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-4">
                                <span
                                    class="whitespace-no-wrap">Total loan penalties balance</span>
                            </p>
                        </div>
                    </div>
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
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetInfoButton from '@/Jetstream/InfoButton.vue'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import LoanStatistics from '@/Pages/Dashboard/LoanStatistics.vue'
import LoanStatusPieChart from '@/Pages/Dashboard/LoanStatusPieChart.vue'
import LoanCollectionOverview from '@/Pages/Dashboard/LoanCollectionOverview.vue'

export default {
    components: {
        AppLayout,
        JetDialogModal,
        JetDangerButton,
        JetSuccessButton,
        JetSecondaryButton,
        JetLabel,
        SelectInput,
        JetInfoButton,
        LoanStatistics,
        LoanStatusPieChart,
        LoanCollectionOverview,
    },
    props: {
        categories: Object,
        designations: Object,
        totalMembers: String,
        totalLoanApplications: String,
        totalLoansRejected: String,
        totalLoansPending: String,
        totalLoansApproved: String,
        totalLoanPrincipal: String,
        totalLoanPrincipalRepaid: String,
        totalLoanPrincipalOutstanding: String,
        totalLoanInterest: String,
        totalLoanInterestRepaid: String,
        totalLoanInterestOutstanding: String,
        totalLoanFees: String,
        totalLoanFeesRepaid: String,
        totalLoanFeesOutstanding: String,
        totalLoanPenalties: String,
        totalLoanPenaltiesRepaid: String,
        totalLoanPenaltiesOutstanding: String,
        totalLoanOutstandingAmount: String,
        totalLoanRepaidAmount: String,
    },
    data() {
        return {
            form: this.$inertia.form({
                widgets: [],
                processing: false
            }),
            pageTitle: "Dashboard",
            pageDescription: "Dashboard",
            showAddWidgetModal: false,
        }
    },
    methods: {},
    computed: {},
    mounted() {

    }
}
</script>
<style scoped>

</style>
