<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>
        <div class="mx-auto">
            <div class="grid sm:grid-cols-1 md:grid-cols-4 gap-4">
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs"> Total Loan Applications </h5>
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
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Loan Applications Pending </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanApplicationsPending
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-yellow-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Loan Applications Approved </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanApplicationsApproved
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Loan Applications Rejected </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{
                                            totalLoanApplicationsRejected
                                        }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Total Loans Amount </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ totalLoanAmount }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-blue-500">
                                        <font-awesome-icon icon="money-bill"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Total Loans Amount Paid </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ totalLoanRepaid }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                                        <font-awesome-icon icon="dollar-sign"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div
                        class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
                        <div class="flex-auto p-4">
                            <div class="flex flex-wrap">
                                <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                                    class="text-gray-500 uppercase font-bold text-xs">Total Loans Amount Balance </h5>
                                    <span
                                        class="font-semibold text-xl text-gray-800"> {{ totalLoanBalance }} </span>
                                </div>
                                <div class="relative w-auto pl-4 flex-initial">
                                    <div
                                        class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                                        <font-awesome-icon icon="minus"/>
                                    </div>
                                </div>
                            </div>
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
import AppLayout from '@/Pages/MemberPortal/Layouts/AppLayout.vue'


export default {
    components: {
        AppLayout,
    },
    props: {
        totalLoans: String,
        totalLoanAmount: String,
        totalLoanApplications: String,
        totalLoanApplicationsApproved: String,
        totalLoanApplicationsRejected: String,
        totalLoanApplicationsPending: String,
        totalLoanRepaid: String,
        totalLoanBalance: String,

    },
    data() {
        return {
            pageTitle: "Dashboard",
            pageDescription: "Dashboard",
        }
    },
    mounted() {

    },
    methods: {}
}
</script>
