<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('statement.index')">
                    Loan Statement
                </inertia-link>
            </h2>
        </template>
        <div class=" mx-auto">
            <form @submit.prevent="submit">
                <div class="bg-white shadow-xl sm:rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 hideOnPrint">
                        <div>
                            <jet-label for="member_id" value="Member" />
                            <Multiselect v-model="selectedMember" @select="changeMember" v-bind="membersMultiSelect" />
                        </div>
                        <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-2 ">
                            <div>
                                <jet-label for="start_date" value="Start Date" />
                                <jet-input id="start_date" type="date" class="block w-full" v-model="form.start_date" />
                                <jet-input-error :message="form.errors.start_date" class="mt-2" />
                            </div>
                            <div>
                                <jet-label for="start_date" value="End Date" />
                                <jet-input id="end_date" type="date" class="block w-full" v-model="form.end_date" />
                                <jet-input-error :message="form.errors.end_date" class="mt-2" />
                            </div>

                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-2 ">
                            <div>
                                <jet-label for="duration" value="Duration" />
                                <select id="duration" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full" v-model="form.duration">
                                    <option value="This Month">This Month</option>
                                    <option value="Previous Month">Previous Month</option>
                                    <option value="This Year">This Year</option>
                                    <option value="Previous Year">Previous Year</option>
                                </select>

                            </div>
                        </div> -->
                        <div>
                            <jet-button class="mt-5 mx-1" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing" @click="submit()">
                                Search
                            </jet-button>
                            <jet-button class="mt-5 mx-1" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing" @click="downloadFile()" type="button" v-if="loan">
                                Download
                            </jet-button>
                            <jet-button class="mt-5 mx-1" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing" type="button" @click="printReport()" v-if="loan">
                                Print
                            </jet-button>
                        </div>
                    </div>
                    <div class="mt-5 mx-auto" v-if="loan">
                        <div class="bg-white rounded shadow overflow-x-auto">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                <div>
                                    <h3>Name: <span style="text-decoration: underline">{{ (CurrentMember.first_name?CurrentMember.first_name:'') +' '+ (CurrentMember.middle_name?CurrentMember.middle_name:'') +' '+(CurrentMember.last_name?CurrentMember.last_name:'') }}</span></h3>
                                </div>
                                <div>
                                    <h3>ID: <span style="text-decoration: underline">{{ CurrentMember.identification_number }}</span></h3>
                                </div>
                                <div>
                                    <h3>Cell: <span style="text-decoration: underline">{{ CurrentMember.contact_number }}</span></h3>
                                </div>
                                <div>
                                    <h3>Address: <span style="text-decoration: underline">{{ CurrentMember.address }}</span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded shadow overflow-x-auto">
                            <table id="reportTable" class="mt-4 pretty displayschedule" >
                                <thead>
                                    <tr>
                                        <th class="empty"></th>
                                        <th>Contract</th>
                                        <th>Paid</th>
                                        <!-- <th>Waived</th> -->
                                        <!-- <th>Written off</th> -->
                                        <th>Outstanding</th>
                                        <!-- <th>Overdue</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Principal</th>
                                        <td>{{ $filters.formatNumber(loan.principal_disbursed_derived) }}</td>
                                        <td>{{ $filters.formatNumber(loan.principal_repaid_derived) }}</td>
                                        <!-- <td>0</td> -->
                                        <!-- <td>{{ $filters.formatNumber(loan.principal_written_off_derived) }}</td> -->
                                        <td>{{ $filters.formatNumber(loan.principal_outstanding_derived) }}</td>
                                        <!-- <td>{{ $filters.formatNumber(loan.principal_overdue) }}</td> -->
                                    </tr>
                                    <tr>
                                        <th>Interest</th>
                                        <td>{{ $filters.formatNumber(loan.interest_disbursed_derived) }}</td>
                                        <td>{{ $filters.formatNumber(loan.interest_repaid_derived) }}</td>
                                        <!-- <td>{{ $filters.formatNumber(loan.interest_waived_derived) }}</td> -->
                                        <!-- <td>{{ $filters.formatNumber(loan.interest_written_off_derived) }}</td> -->
                                        <td>{{ $filters.formatNumber(loan.interest_outstanding_derived) }}</td>
                                        <!-- <td>{{ $filters.formatNumber(loan.interest_overdue) }}</td> -->
                                    </tr>
                                    <tr>
                                        <th>Fees</th>
                                        <td>{{ $filters.formatNumber(loan.fees_disbursed_derived) }}</td>
                                        <td>{{ $filters.formatNumber(loan.fees_repaid_derived) }}</td>
                                        <!-- <td>{{ $filters.formatNumber(loan.fees_waived_derived) }}</td> -->
                                        <!-- <td>{{ $filters.formatNumber(loan.fees_written_off_derived) }}</td> -->
                                        <td>{{ $filters.formatNumber(loan.fees_outstanding_derived) }}</td>
                                        <!-- <td>{{ $filters.formatNumber(loan.fees_overdue) }}</td> -->
                                    </tr>
                                    <tr>
                                        <th>Penalties</th>
                                        <td>{{ $filters.formatNumber(loan.penalties_disbursed_derived) }}</td>
                                        <td>{{ $filters.formatNumber(loan.penalties_repaid_derived) }}</td>
                                        <!-- <td>{{ $filters.formatNumber(loan.penalties_waived_derived) }}</td> -->
                                        <!-- <td>{{ $filters.formatNumber(loan.penalties_written_off_derived) }}</td> -->
                                        <td>{{ $filters.formatNumber(loan.penalties_outstanding_derived) }}</td>
                                        <!-- <td>{{ $filters.formatNumber(loan.penalties_overdue) }}</td> -->
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th>{{ $filters.formatNumber(loan.total_disbursed_derived) }}</th>
                                        <th>{{ $filters.formatNumber(loan.total_repaid_derived) }}</th>
                                        <!-- <th>{{ $filters.formatNumber(loan.total_waived_derived) }}</th> -->
                                        <!-- <th>{{ $filters.formatNumber(loan.total_written_off_derived) }}</th> -->
                                        <th>{{ $filters.formatNumber(loan.total_outstanding_derived) }}</th>
                                        <!-- <th>{{ $filters.formatNumber(loan.arrears_amount) }}</th> -->
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-4 relative overflow-x-auto">
                            <table class=" whitespace-no-wrap table-auto">
                                <thead class="bg-gray-50">
                                    <tr class="text-left font-bold">
                                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">ID</th>
                                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Submitted On</th>
                                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Transaction Date</th>
                                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Transaction Description</th>
                                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Debit</th>
                                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Credit</th>
                                        <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!results.length">
                                        <td colspan="8" class="px-6 py-4 text-center">
                                            No records Yet
                                        </td>
                                    </tr>
                                    <tr v-for="result in results" :key="result.id"
                                        class="hover:bg-gray-100 focus-within:bg-gray-100">
                                        <td class="border-t px-6 py-4">
                                            <span>
                                                {{ result.id }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <span>
                                                {{ result.created_on }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <span>
                                                {{ result.submitted_on }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <span v-if="result.type">
                                                {{ result.type.name }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <span>
                                                {{ $filters.formatNumber(result.debit,2) }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <span>
                                                {{ $filters.formatNumber(result.credit,2) }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <span>
                                                {{ $filters.formatNumber(result.balance,2) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">

                        <!-- Print Button -->
                        <!-- Print Button -->
                        <!-- <jet-button class="ml-4" @click="printForm" :style="{ backgroundColor: 'blue', color: 'white' }">
                            Print
                        </jet-button> -->

                    </div>
                </div>
            </form>
        </div>
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
        return { value: item.id, label: item.name + ('(#' + item.id + ')') }
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
        item.display_name = item.name + ' (#' + item.identification_number + ')'
        return item;
    })
}


import AppLayout from '@/Layouts/AppLayout.vue'
import JetButton from "@/Jetstream/Button.vue";
import Pagination from '@/Jetstream/Pagination.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        loan: Object,
        results: Object,
        paymentTypes: Object,
        CurrentMember: Object
    },
    // props: {
    //     LoanStatement: Object
    // },
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
            form: this.$inertia.form({
                // member_type: 'member',
                member_id: null,
            }),
            usersMultiSelect: {
                placeholder: 'Search for Staff',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query || this.form.loan_officer_id)
                }
            },
            membersMultiSelect: {
                valueProp: 'id',
                label: 'display_name',
                selectedMember: null,
                placeholder: 'Search for Member',
                filterResults: false,
                object: true,
                minChars: 2,
                resolveOnLoad: true,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchMembers(query || this.form.member_id)
                }
            },
            selectedMember: null,
            selectedProduct: null,
            selectedCharge: null,
            pageTitle: "Create Loan Application",
            pageDescription: "Create Loan Application",
            reportData: null
        }

    },
    mounted() {

    },
    methods: {
        printReport() {
            window.print();

        },
        submit() {
            this.form.get(this.route('statement.index'), {})
        },
        printForm() {
            window.print();
        },
        changeMember() {
            this.form.member_id = this.selectedMember.id;
            this.form.member_account_holder = this.selectedMember.first_name + ' ' + this.selectedMember.middle_name + ' ' + this.selectedMember.last_name;
        },
        downloadFile() {
            // Get the table element
            const table = document.getElementById('reportTable');

            // Create a new Blob
            let csvContent = '';

            // Loop through each row of the table
            for (let row of table.rows) {
                const rowData = Array.from(row.cells).map(cell => cell.innerText.replace(/,/g, '')).join(',');
                csvContent += rowData + '\n'; // Add a new line after each row
            }

            // Create a Blob from the CSV content
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);

            // Create a link element to download the Blob
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'loan_statment.csv'); // Change the file extension for Excel
            document.body.appendChild(link);
            link.click(); // Programmatically click the link to trigger download
            document.body.removeChild(link); // Remove the link after downloading
        }
    },
}
</script>
<style scoped>
@media print {
    body {
        visibility: hidden;
    }

    .hideOnPrint {
        visibility: hidden;
    }

    /* #printDiv {
    visibility: visible;
    position: absolute;
    left: 0;
    top: 0;
  } */
  body {
        margin: 0; /* Remove default margins */
        padding: 0; /* Remove default padding */
    }

    table {
        width: 100%; /* Make the table take full width */
        table-layout: auto; /* Allow the table to adjust its layout */
    }

    th, td {
        overflow: hidden; /* Hide overflow for cells */
        white-space: nowrap; /* Prevent text wrapping */
        text-overflow: ellipsis; /* Add ellipsis for overflowed text */
    }

    /* Optional: Hide horizontal scrollbars */
    * {
        overflow-x: hidden !important;
    }
  table {
        width: 100%; /* Make the table take full width */
        table-layout: auto; /* Allow the table to adjust its layout */
    }

    th, td {
        overflow: hidden; /* Hide overflow for cells */
        white-space: nowrap; /* Prevent text wrapping */
        text-overflow: ellipsis; /* Add ellipsis for overflowed text */
    }
}
</style>
