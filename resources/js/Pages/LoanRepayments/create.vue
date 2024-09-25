<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.repayments.index')">
                    Loan/Repayment
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Add Transection
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 ">
                        <div>
                            <!-- <jet-label for="member_id" value="Member"/> -->
                            <!-- <Multiselect
                                v-model="selectedMember"
                                @select="changeMember"
                                v-bind="membersMultiSelect"
                                :required="true"
                            /> -->
                        </div>
                    </div>
                    <div >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                              <div>
                                <jet-label for="c" value="Amount"/>
                                <jet-input id="amount" type="number"
                                           class="block w-full" v-model="form.amount"/>
                                <jet-input-error :message="form.errors.amount" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="date" value="Date"/>
                                <jet-input id="date" type="date"
                                           class="block w-full" v-model="form.date"/>
                                <jet-input-error :message="form.errors.date" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="repayment_type" value="Repayment Type"/>
                                <select id="repayment_type" class="block w-full" v-model="form.repayment_type">
                                    
                                    <option value="EFT">EFT</option>
                                    <option value="Stop Order">Stop Order</option>
                                </select>
                                <jet-input-error :message="form.errors.repayment_type" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="receipt" value="Receipt #"/>
                                <jet-input id="receipt" type="text"
                                           class="block w-full" v-model="form.receipt"/>
                                <jet-input-error :message="form.errors.receipt" class="mt-2"/>
                            </div>
                            
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                        
                            <div>
                                <jet-label for="account_number" value="Account Number"/>
                                <jet-input id="account_number" type="text"
                                           class="block w-full" v-model="form.account_number"/>
                                <jet-input-error :message="form.errors.account_number" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="cheque" value="Cheque #"/>
                                <jet-input id="cheque" type="text"
                                           class="block w-full" v-model="form.cheque"/>
                                <jet-input-error :message="form.errors.cheque" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                         
                            <div>
                                <jet-label for="routing_code" value="Routing Code"/>
                                <jet-input id="routing_code" type="text"
                                           class="block w-full" v-model="form.routing_code"/>
                                <jet-input-error :message="form.errors.routing_code" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="bank" value="Bank"/>
                                <jet-input id="bank" type="text"
                                           class="block w-full" v-model="form.bank"/>
                                <jet-input-error :message="form.errors.bank" class="mt-2"/>
                            </div>
                        </div>
                        <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                        
                            
                        </div> -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                              <div>
                                <jet-label for="description" value="Description"/>
                                <jet-input id="description" type="text"
                                           class="block w-full" v-model="form.description"/>
                                <jet-input-error :message="form.errors.description" class="mt-2"/>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">

                                 <!-- Print Button -->
                   <!-- Print Button -->
                        <!-- <jet-button class="ml-4" @click="printForm" :style="{ backgroundColor: 'blue', color: 'white' }">
                            Print
                        </jet-button> -->
                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                            Save
                        </jet-button>
                    </div>

                </form>
            </div>
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
        return {value: item.id, label: item.name + ('(#' + item.id + ')')}
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
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        products: Object,
        checklists: Object,
        purposes: Object,
        customFields: Object,
        categories: Object,
        designations: Object,
        member_id: String,
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
            form: this.$inertia.form({
                member_type: 'member',
                member_id:null,
                amount: null,
                account_number: null,
                repayment_type: null,
                routing_code: null,
                date: null,
                receipt: null,
                cheque: null,
                bank: null,
                description: null,
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
            pageTitle: "Create Repayment Transection",
            pageDescription: "Create Repayment Transection",
        }

    },
    mounted() {

    },
    methods: {
        submit() {
            this.form.post(this.route('loans.repayments.store'), {})

        },
        printForm() {
            window.print();
        },
        addItem() {
            if (this.selectedCharge) {
                let existing = false;
                this.form.selected_charges.forEach(item => {
                    if (item.id === this.selectedCharge.id) {
                        existing = true;
                    }
                });
                if (!existing) {
                    this.form.selected_charges.push(this.selectedCharge)
                }
            }
            this.$refs.chargesMultiselectField.clear()
            this.$refs.chargesMultiselectField.clearSearch()
            this.$refs.chargesMultiselectField.deselect()
            this.$refs.chargesMultiselectField.close()
        },
        removeItem(index) {
            this.form.items.splice(index, 1);
            this.updateItems();
        },
        changeMember() {
//             receipt
// cheque
// bank
            //this.form.cheque = this.selectedMember.account_number;
            this.form.member_id = this.selectedMember.id;
            this.form.receipt = this.selectedMember.first_name+' '+this.selectedMember.middle_name+' '+this.selectedMember.last_name;
            //this.form.description = this.selectedMember.description;
        },
        changeLoanProduct() {
            if (this.selectedProduct) {
                this.form.loan_product_id = this.selectedProduct.id;
                this.form.applied_amount = this.selectedProduct.default_principal;
                this.form.loan_term = this.selectedProduct.default_loan_term;
                this.form.repayment_frequency = this.selectedProduct.repayment_frequency;
                this.form.repayment_frequency_type = this.selectedProduct.repayment_frequency_type;
                this.form.loan_application_checklist_id = this.selectedProduct.loan_application_checklist_id;
                this.form.interest_rate = this.selectedProduct.default_interest_rate;
            }
        },

    },
    computed: {
        availableCharges: function () {
            let charges = [];
            if (this.selectedProduct) {
                this.selectedProduct.charges.forEach(item => {
                    charges.push({
                        'id': item.id,
                        'loan_charge_id': item.loan_charge_id,
                        'name': item.charge?.name,
                        'type': item.charge?.type?.name,
                        'option': item.charge?.option?.name,
                        'amount': item.charge?.amount,
                    })
                })
            }
            return charges;
        },
    },
    watch: {}
}
</script>
<style scoped>

</style>
