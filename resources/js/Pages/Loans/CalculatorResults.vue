<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">
                    Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Calculator
            </h2>
        </template>


        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <div class="overflow-x-auto">
                    <table class="pretty displayschedule" id="repaymentschedule" style="margin-top: 20px;">
                        <colgroup span="2"></colgroup>
                        <colgroup span="3">
                            <col class="lefthighlightcol">
                            <col>
                            <col class="righthighlightcol">
                        </colgroup>
                        <colgroup span="3">
                            <col class="lefthighlightcol">
                            <col>
                            <col class="righthighlightcol">
                        </colgroup>
                        <colgroup span="3"></colgroup>
                        <thead>
                        <tr>
                            <th class="empty" scope="colgroup" colspan="3">&nbsp;</th>
                            <th class="highlightcol" scope="colgroup"
                                colspan="3">Loan Amount and Balance
                            </th>
                            <th class="highlightcol" scope="colgroup"
                                colspan="3">Total Cost of Loan
                            </th>
                        </tr>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Date</th>
                            <th scope="col">#Days</th>
                            <th class="lefthighlightcolheader"
                                scope="col">Disbursement
                            </th>
                            <th scope="col">Principal Due</th>
                            <th class="righthighlightcolheader"
                                scope="col">Principal Balance
                            </th>

                            <th class="lefthighlightcolheader"
                                scope="col">Interest Due
                            </th>
                            <th scope="col">Fees</th>
                            <th scope="col">Total Due</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td scope="row"></td>
                            <td>{{ loan_details["disbursement_date"] }}</td>
                            <td></td>
                            <td class="lefthighlightcolheader">
                                {{ $filters.formatNumber(loan_details["principal"], loan_details["decimals"]) }}
                            </td>
                            <td></td>
                            <td class="righthighlightcolheader">
                                {{ $filters.formatNumber(loan_details["principal"], loan_details["decimals"]) }}
                            </td>
                            <td class="lefthighlightcolheader"></td>
                            <td>{{ $filters.formatNumber(loan_details["disbursement_fees"], loan_details["decimals"]) }}</td>
                            <td>{{ $filters.formatNumber(loan_details["disbursement_fees"], loan_details["decimals"]) }}</td>
                        </tr>
                        <tr v-for="(item,index) in schedules">
                            <td scope="row">{{ index+1 }}</td>
                            <td>{{ item['due_date'] }}</td>
                            <td>{{ item['days'] }}</td>

                            <td class="lefthighlightcolheader"></td>
                            <td>{{ $filters.formatNumber(item['principal'], loan_details["decimals"]) }}</td>
                            <td class="righthighlightcolheader">
                                {{ $filters.formatNumber(item['balance'], loan_details["decimals"]) }}
                            </td>
                            <td class="lefthighlightcolheader">
                                {{ $filters.formatNumber(item['interest'], loan_details["decimals"]) }}
                            </td>
                            <td>{{ $filters.formatNumber(item['fees'], loan_details["decimals"]) }}</td>
                            <td>{{ $filters.formatNumber(item['total_due'], loan_details["decimals"]) }}</td>
                        </tr>
                        </tbody>
                        <tfoot class="ui-widget-header">
                        <tr>
                            <th colspan="2">Total</th>
                            <th>{{ loan_details["total_days"] }}</th>
                            <th class="lefthighlightcolheader">
                                {{ $filters.formatNumber(loan_details["principal"], loan_details["decimals"]) }}
                            </th>
                            <th>{{ $filters.formatNumber(loan_details["principal"], loan_details["decimals"]) }}</th>
                            <th class="righthighlightcolheader">&nbsp;</th>
                            <th class="lefthighlightcolheader">
                                {{ $filters.formatNumber(loan_details['total_interest'], loan_details["decimals"]) }}
                            </th>
                            <th>{{ $filters.formatNumber(loan_details["total_fees"], loan_details["decimals"]) }}</th>
                            <th>{{ $filters.formatNumber(loan_details["total_due"], loan_details["decimals"]) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="print:hidden flex items-center justify-end mt-4">

                    <jet-button class="ml-4" @click="print">
                        Print
                    </jet-button>
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
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        loan_details: Object,
        schedules: Object,
    },
    components: {
        SelectInput,
        AppLayout,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetInputError,
        FileInput,
        TextareaInput,

    },
    data() {
        return {
            selectedMember: null,
            selectedProduct: null,
            selectedCharge: null,
            pageTitle: "Loan Calculator",
            pageDescription: "Loan Calculator",
        }

    },
    mounted() {

    },
    methods: {
        print() {
           window.print()

        },

    },
}
</script>
<style scoped>

</style>
