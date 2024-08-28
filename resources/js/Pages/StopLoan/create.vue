<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.stop_loan.index')">
                    Stop Order
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Create
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 ">
                        <div>
                            <jet-label for="member_id" value="Member"/>
                            <Multiselect
                                v-model="selectedMember"
                                @select="changeMember"
                                v-bind="membersMultiSelect"
                                :required="true"
                            />
                        </div>
                    </div>
                    <div >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                              <div>
                                <jet-label for="account_holder" value="Account Holder"/>
                                <jet-input id="account_holder" type="text"
                                           class="block w-full" v-model="form.account_holder"/>
                                <jet-input-error :message="form.errors.account_holder" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="account_number" value="Account Number"/>
                                <jet-input id="account_number" type="text"
                                           class="block w-full" v-model="form.account_number"/>
                                <jet-input-error :message="form.errors.account_number" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                              <div>
                                <jet-label for="branch_code" value="Branch Code"/>
                                <jet-input id="branch_code" type="text"
                                           class="block w-full" v-model="form.branch_code"/>
                                <jet-input-error :message="form.errors.branch_code" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="member_account_holder" value="Member Account Holder"/>
                                <jet-input id="member_account_holder" type="text"
                                           class="block w-full" v-model="form.member_account_holder"/>
                                <jet-input-error :message="form.errors.member_account_holder" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div>
                                <jet-label for="member_account_number" value="Member Account Number"/>
                                <jet-input id="member_account_number" type="text"
                                           class="block w-full" v-model="form.member_account_number"/>
                                <jet-input-error :message="form.errors.member_account_number" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="monthly_installment" value="Monthly Installment"/>
                                <jet-input id="monthly_installment" type="text"
                                           class="block w-full" v-model="form.monthly_installment"/>
                                <jet-input-error :message="form.errors.monthly_installment" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div>
                                <jet-label for="stop_order_date" value="Stop Order Date"/>
                                <jet-input id="stop_order_date" type="date"
                                           class="block w-full" v-model="form.stop_order_date"/>
                                <jet-input-error :message="form.errors.stop_order_date" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="member_branch_code" value="Member Branch code"/>
                                <jet-input id="member_branch_code" type="text"
                                           class="block w-full" v-model="form.member_branch_code"/>
                                <jet-input-error :message="form.errors.member_branch_code" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                              <div>
                                <jet-label for="reference" value="Reference"/>
                                <jet-input id="reference" type="text"
                                           class="block w-full" v-model="form.reference"/>
                                <jet-input-error :message="form.errors.reference" class="mt-2"/>
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
                account_holder: 'Member of Parliament Pension Fund',
                account_number: '1199 0 39 96 21',
                branch_code: '360164',
                monthly_installment: null,
                stop_order_date: null,
                member_account_holder: null,
                member_account_number: null,
                member_branch_code: null,
                reference: null,
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
        }

    },
    mounted() {

    },
    methods: {
        submit() {
            this.form.post(this.route('loans.stop_loan.store'), {})

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
//             member_account_holder
// member_account_number
// member_branch_code
            //this.form.member_account_number = this.selectedMember.account_number;
            this.form.member_id = this.selectedMember.id;
            this.form.member_account_holder = this.selectedMember.first_name+' '+this.selectedMember.middle_name+' '+this.selectedMember.last_name;
            //this.form.reference = this.selectedMember.reference;
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
