<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">
                    Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Calculator
            </h2>
        </template>


        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-2 ">
                        <div>
                            <jet-label for="loan_product_id" value="Product"/>
                            <Multiselect
                                v-model="selectedProduct"
                                @select="changeLoanProduct"
                                value-prop="id"
                                label="name"
                                :options="products"
                                :object="true"
                                :required="true"
                            />
                        </div>

                    </div>
                    <div v-if="selectedProduct">
                        <h3 class="mt-4 font-bold">Terms</h3>
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-2 mt-4">
                            <div class="">
                                <jet-label for="applied_amount" value="Principal"/>
                                <jet-input id="applied_amount" type="text"
                                           class="block w-full" v-model="form.applied_amount"/>
                                <jet-input-error :message="form.errors.applied_amount" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-4">
                            <div>
                                <jet-label for="loan_term" value="Loan Term"/>
                                <jet-input type="number" id="loan_term" class="block w-full"
                                           v-model="form.loan_term" required/>
                                <jet-input-error :message="form.errors.loan_term"
                                                 class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="repayment_frequency" value="Repayment Frequency"/>
                                <jet-input type="number" id="repayment_frequency" class="block w-full"
                                           v-model="form.repayment_frequency" required/>
                                <jet-input-error :message="form.errors.repayment_frequency"
                                                 class="mt-2"/>
                            </div>
                            <div class="">
                                <jet-label for="repayment_frequency_type" value="Repayment Frequency Type"/>
                                <select-input id="repayment_frequency_type" class="block w-full"
                                              v-model="form.repayment_frequency_type" required>
                                    <option value="days">Days</option>
                                    <option value="weeks">Weeks</option>
                                    <option value="months">Months</option>
                                </select-input>
                                <jet-input-error :message="form.errors.repayment_frequency_type" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-4">
                            <div>
                                <jet-label for="interest_rate" value="Interest Rate"/>
                                <jet-input type="text" id="interest_rate" class="block w-full"
                                           v-model="form.interest_rate"
                                           :readonly="selectedProduct.disallow_interest_rate_adjustment"/>
                                <jet-input-error :message="form.errors.interest_rate"
                                                 class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="expected_disbursement_date" value="Disbursement Date"/>
                                <flat-pickr
                                    v-model="form.expected_disbursement_date"
                                    class="form-control w-full"
                                    placeholder="Select date"
                                    name="expected_disbursement_date">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.expected_disbursement_date" class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="expected_first_payment_date" value="First Repayment Date"/>
                                <flat-pickr
                                    v-model="form.expected_first_payment_date"
                                    class="form-control w-full"
                                    placeholder="Select date"
                                    name="expected_first_payment_date">
                                </flat-pickr>
                                <jet-input-error :message="form.errors.expected_first_payment_date" class="mt-2"/>

                            </div>
                        </div>


                    </div>
                    <div class="flex items-center justify-end mt-4">

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
        funds: Object,
        purposes: Object,
        customFields: Object,
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
                member_id: null,
                group_id: null,
                loan_product_id: null,
                fund_id: null,
                loan_purpose_id: null,
                loan_officer_id: null,
                expected_first_payment_date: moment().format("YYYY-MM-DD"),
                expected_disbursement_date: moment().format("YYYY-MM-DD"),
                submitted_on_date: moment().format("YYYY-MM-DD"),
                applied_amount: null,
                loan_term: null,
                repayment_frequency: null,
                repayment_frequency_type: null,
                interest_rate: null,
                selected_charges: [],
                custom_fields: this.customFields,
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
            productsMultiSelect: {
                valueProp: 'id',
                label: 'name',
                selected_patient: null,
                placeholder: 'Search for Loan Product',
                filterResults: false,
                object: true,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchPatients(query)
                }
            },
            membersMultiSelect: {
                valueProp: 'id',
                label: 'name',
                selectedMember: null,
                placeholder: 'Search for Member',
                filterResults: false,
                object: true,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchMembers(query || this.form.member_id)
                }
            },
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
        submit() {
            this.form.post(this.route('loans.process_loan_calculator'), {})

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
            this.form.loan_officer_id = this.selectedMember.loan_officer_id;
            this.form.member_id = this.selectedMember.id;
        },
        changeLoanProduct() {
            if (this.selectedProduct) {
                this.form.loan_product_id = this.selectedProduct.id;
                this.form.applied_amount = this.selectedProduct.default_principal;
                this.form.loan_term = this.selectedProduct.default_loan_term;
                this.form.repayment_frequency = this.selectedProduct.repayment_frequency;
                this.form.repayment_frequency_type = this.selectedProduct.repayment_frequency_type;
                this.form.fund_id = this.selectedProduct.fund_id;
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
