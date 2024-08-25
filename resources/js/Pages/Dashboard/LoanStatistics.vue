<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
            <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                        class="text-gray-500 uppercase font-bold"> Loans Disbursed </h5>
                        <span class="font-semibold text-xl text-gray-800">
                                {{ loansDisbursedAmount }}
                            </span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                        <div
                            class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                            <font-awesome-icon icon="money-bill"/>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">
                        <span :class="loansDisbursedAmountChangeClass" class="mr-2"><i class="fas fa-arrow-up"></i> {{
                                loansDisbursedAmountChange
                            }}% </span>
                    <span class="whitespace-no-wrap">Since last month</span>
                </p>
            </div>
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
            <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                        class="text-gray-500 uppercase font-bold"> Total Repayments </h5>
                        <span class="font-semibold text-xl text-gray-800">
                                {{ loansRepaymentsAmount }}
                            </span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                        <div
                            class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-green-500">
                            <font-awesome-icon icon="dollar-sign"/>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">
                    <span :class="loansRepaymentsAmountChangeClass" class=" mr-2"><i
                        class="fas fa-arrow-up"></i> {{ loansRepaymentsAmountChange }}% </span>
                    <span class="whitespace-no-wrap">Since last month</span>
                </p>
            </div>
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
            <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                        class="text-gray-500 uppercase font-bold"> Outstanding Amount </h5>
                        <span class="font-semibold text-xl text-gray-800">
                                {{ loansOutstandingAmount }}
                            </span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                        <div
                            class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-orange-500">
                            <font-awesome-icon icon="money-bill"/>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">
                    <span class="whitespace-no-wrap">Shows total outstanding amount</span>
                </p>
            </div>
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white rounded mb-6 xl:mb-0 shadow-lg">
            <div class="flex-auto p-4">
                <div class="flex flex-wrap">
                    <div class="relative w-full pr-4 max-w-full flex-grow flex-1"><h5
                        class="text-gray-500 uppercase font-bold"> Total Arrears </h5>
                        <span class="font-semibold text-xl text-gray-800">
                                {{ loansArrearsAmount }}
                            </span>
                    </div>
                    <div class="relative w-auto pl-4 flex-initial">
                        <div
                            class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full bg-red-500">
                            <font-awesome-icon icon="minus"/>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">
                    <span class="whitespace-no-wrap">Shows total arrears amount</span>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

export default {
    name: "LoanStatistics",
    components: {FontAwesomeIcon},
    data() {
        return {
            loansDisbursedAmount: 0,
            loansDisbursedAmountChange: 0,
            loansDisbursedAmountChangeClass: 0,
            loansRepaymentsAmount: 0,
            loansRepaymentsAmountChange: 0,
            loansRepaymentsAmountChangeClass: 0,
            loansOutstandingAmount: 0,
            loansOutstandingAmountChange: 0,
            loansOutstandingAmountChangeClass: 0,
            loansArrearsAmount: 0,
            loansArrearsAmountChange: 0,
            loansArrearsAmountChangeClass: 0,
        }
    },
    methods: {
        getStatistics() {
            axios.get(route('dashboard.get_loan_statistics')).then(response => {
                this.loansDisbursedAmount = response.data.loansDisbursedAmount
                this.loansDisbursedAmountChange = response.data.loansDisbursedAmountChange
                this.loansDisbursedAmountChangeClass = response.data.loansDisbursedAmountChangeClass
                this.loansRepaymentsAmount = response.data.loansRepaymentsAmount
                this.loansRepaymentsAmountChange = response.data.loansRepaymentsAmountChange
                this.loansRepaymentsAmountChangeClass = response.data.loansRepaymentsAmountChangeClass
                this.loansOutstandingAmount = response.data.loansOutstandingAmount
                this.loansArrearsAmount = response.data.loansArrearsAmount
            })
        }
    },
    mounted() {
        this.getStatistics()
    }
}
</script>

<style scoped>

</style>
