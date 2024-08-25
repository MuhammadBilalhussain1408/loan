<template>
    <div class="w-full bg-white rounded mb-6 xl:mb-0 shadow-lg">

        <div class="flex justify-between items-center border-b p-2">
            <h3>Loan Collection Statistics</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div class="text-center">
                <h5 class="text-semibold no-margin">
                    {{ paymentsToday }}
                </h5>
                <span class="text-gray-400">Today </span>
            </div>
            <div class="text-center">
                <h5 class="text-semibold no-margin">
                    {{ paymentsThisWeek }}
                </h5>
                <span class="text-gray-400">This Week </span>
            </div>
            <div class="text-center">
                <h5 class="text-semibold no-margin">
                    {{ paymentsThisMonth }}
                </h5>
                <span class="text-gray-400">This Month </span>
            </div>
        </div>
        <div class="text-center mt-4 p-2">
            <h4 class="text center text-gray-400">Monthly Target</h4>
            <div class="mt-2 w-full bg-gray-200 rounded-full mb-4 dark:bg-gray-700" :title="paymentsProgress+' %'">
                <div
                    class="bg-green-600 text-green-100 rounded-full dark:bg-green-500  text-center p-0.5 leading-none text-xs font-medium "
                    :style="'width: '+paymentsProgress+'%'">{{ paymentsProgress }}%
                </div>
            </div>
        </div>
        <div class="mt-4  p-2 text-center">
            <h4 class="text center text-gray-400 mb-4">Collection Overview</h4>
            <bar :chart-data="loanCollectionOverviewData" ref="loanCollectionOverviewChart"
                 :chart-options="{responsive:true,maintainAspectRatio: false}" :height="0"/>
        </div>
    </div>
</template>

<script>
import {Bar} from "vue-chartjs";
import {Chart, registerables} from "chart.js";

Chart.register(...registerables);
export default {
    name: "LoanCollectionOverview",
    components: {Bar},
    data() {
        return {
            paymentsToday: 0,
            paymentsThisWeek: 0,
            paymentsThisMonth: 0,
            paymentsExpectedThisMonth: 0,
            paymentsProgress: 60,
            loanCollectionOverviewData: {
                labels: [],
                datasets: []
            },
        }
    },
    methods: {
        getCollectionOverview() {
            axios.get(route('dashboard.get_loan_collection_overview')).then(response => {
                this.paymentsToday = response.data.paymentsToday
                this.paymentsThisWeek = response.data.paymentsThisWeek
                this.paymentsThisMonth = response.data.paymentsThisMonth
                this.paymentsExpectedThisMonth = response.data.paymentsExpectedThisMonth
                this.paymentsProgress = response.data.paymentsProgress
                let labels = [];
                let expected = [];
                let actual = [];
                response.data.chartData.forEach(item => {
                    labels.push(item.label);
                    expected.push(item.expected);
                    actual.push(item.actual);
                })
                this.loanCollectionOverviewData = {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Actual Payments',
                            data: actual,
                            backgroundColor: 'green',
                            borderColor: 'green',
                            borderWidth: 1
                        },
                        {
                            label: 'Expected Payments',
                            data: expected,
                            backgroundColor: 'blue',
                            borderColor: 'blue',
                            borderWidth: 1
                        }
                    ]
                }
            })
        }
    },
    mounted() {
        this.getCollectionOverview()
    }
}
</script>

<style scoped>

</style>
