<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.applications.index')">
                    Loan Applications
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div class="">
                                <jet-label for="applied_amount" value="Amount"/>
                                <jet-input id="applied_amount" type="text"
                                           class="block w-full" v-model="form.applied_amount"/>
                                <jet-input-error :message="form.errors.applied_amount" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="loan_application_checklist_id" value="Checklist"/>
                                <Multiselect
                                    v-model="form.loan_application_checklist_id"
                                    :options="checklists"
                                    label="name"
                                    value-prop="id"
                                />
                                <jet-input-error :message="form.errors.loan_application_checklist_id" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-4">
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
                            <div>
                                <jet-label for="interest_rate" value="Interest Rate"/>
                                <jet-input type="text" id="interest_rate" class="block w-full"
                                           v-model="form.interest_rate"
                                           :readonly="selectedProduct.disallow_interest_rate_adjustment"/>
                                <jet-input-error :message="form.errors.interest_rate"
                                                 class="mt-2"/>
                            </div>
                        </div>
                        <h3 class="mt-4 font-bold">Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4 mb-4">
                            <div>
                                <jet-label for="loan_officer_id" value="Loan Officer"/>
                                <Multiselect
                                    v-model="form.loan_officer_id"
                                    v-bind="usersMultiSelect"
                                />
                            </div>
                            <div>
                                <jet-label for="loan_purpose_id" value="Loan Purpose"/>
                                <Multiselect
                                    v-model="form.loan_purpose_id"
                                    mode="single"
                                    value-prop="id"
                                    label="name"
                                    :options="purposes"
                                />
                                <jet-input-error :message="form.errors.loan_purpose_id" class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <jet-label for="member_category_id" value="Category"/>
                                <Multiselect
                                    id="member_category_id"
                                    v-model="form.member_category_id"
                                    value-prop="id"
                                    label="name"
                                    :options="categories"
                                />
                                <jet-input-error :message="form.errors.member_category_id" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="member_designation_id" value="Designation"/>
                                <Multiselect
                                    id="member_designation_id"
                                    v-model="form.member_designation_id"
                                    value-prop="id"
                                    label="name"
                                    :options="designations"
                                />
                                <jet-input-error :message="form.errors.member_designation_id" class="mt-2"/>
                            </div>

                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4 mb-4">
                            <div>
                                <div class=" ">
                                    <jet-label for="admin_charges" value="Admin Charges"/>
                                    <jet-input id="admin_charges" type="number"
                                            class="block w-full" disabled v-model="form.admin_charges"/>
                                    <jet-input-error :message="form.errors.admin_charges" class="mt-2"/>
                               </div>
                            </div>
                            <div style="display: none;">
                                <jet-label for="tariff" value="Charges"/>
                                <Multiselect
                                    v-model="selectedCharge"
                                    placeholder="0.05"
                                    :options="availableCharges"
                                    value-prop="id"
                                    label="name"
                                    :object="true"
                                    @select="addItem"
                                    ref="chargesMultiselectField"
                                />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4 mb-4">
                                    <div class=" ">
                                    <jet-label for="Signature" value="Signature"/>
                                    <jet-label for="" value="___________________"/>
                                    
                                  </div>
                            </div>
                        </div>
                        <div class="mt-4 mb-4 overflow-x-auto">
                            <table class="w-full whitespace-no-wrap">
                                <thead class="bg-gray-50">
                                <tr class="text-left font-bold">
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Type</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Amount</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Collected on</th>
                                    <th class="px-6 pt-4 pb-4 font-medium text-gray-500"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in form.selected_charges">
                                    <td class="border-t px-4 py-4">
                                        {{ item.name }}
                                    </td>
                                    <td class="border-t px-4 py-4">
                                        {{ item.type }}
                                    </td>
                                    <td class="border-t px-4 py-4">
                                        <jet-input type="text" class="block w-full"
                                                   v-model="item.amount" v-if="selectedProduct.allow_override"
                                        />
                                        <span v-else>{{ item.amount }}</span>
                                    </td>
                                    <td class="border-t px-4 py-4">
                                        {{ item.option }}
                                    </td>
                                    <td class="border-t  px-4 py-4 flex items-center justify-center">
                                        <div class="text-red-400 mt-2 cursor-pointer">
                                            <font-awesome-icon icon="trash"
                                                               v-on:click="form.selected_charges.splice(index)"
                                                               class="w-4 h-4 mr-2"></font-awesome-icon>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-for="(field,index) in form.custom_fields" class="mt-4 grid grid-cols-1 gap-4">
                            <div v-if="field.type==='text'">
                                <jet-label :for="'field_'+field.id" :value="field.name"/>
                                <jet-input :id="'field_'+field.id" type="text" class="mt-1 block w-full"
                                           v-model="field.field_value"
                                           :required="field.required"/>

                            </div>
                            <div v-if="field.type==='number'">
                                <jet-label :for="'field_'+field.id" :value="field.name"/>
                                <jet-input :id="'field_'+field.id" type="number" class="mt-1 block w-full"
                                           v-model="field.field_value"
                                           :required="field.required"/>

                            </div>
                            <div v-if="field.type==='textarea'">
                                <jet-label :for="'field_'+field.id" :value="field.name"/>
                                <textarea-input :id="'field_'+field.id" type="number" class="mt-1 block w-full"
                                                v-model="field.field_value"
                                                :required="field.required"/>
                            </div>
                            <div v-if="field.type==='dropdown'">
                                <jet-label :for="'field_'+field.id" :value="field.name"/>
                                <select-input :id="'field_'+field.id" class="mt-1 block w-full"
                                              v-model="field.field_value"
                                              :required="field.required">
                                    <option v-for="option in field.options">{{ option }}</option>
                                </select-input>
                            </div>
                            <div v-if="field.type==='file'">
                                <jet-label :for="'field_'+field.id">
                                    {{ field.name }}
                                    <span v-if="field.file" class="ml-2">
                                            <a target="_blank" class="text-indigo-400"
                                               :href="route('files.download',field.file.id)">({{ field.file.name }})</a>
                                        </span>
                                </jet-label>
                                <file-input :id="'field_'+field.id" type="number" class="mt-1 block w-full"
                                            v-model="field.field_value"
                                            :required="field.required"/>
                            </div>
                            <div v-if="field.type==='checkbox'">
                                <jet-label :for="'field_'+field.id">
                                    <div class="flex items-center">
                                        <jet-checkbox :id="'field_'+field.id" v-model:checked="field.field_value"/>
                                        <div class="ml-2">
                                            {{ field.name }}
                                        </div>
                                    </div>
                                </jet-label>

                            </div>
                            <div v-if="field.type==='radio'">
                                <h4>{{ field.name }}</h4>
                                <div class="flex items-center mb-4" v-for="option in field.options">
                                    <input :id="'field_option_'+option" type="radio" :name="field.name"
                                           :value="option" v-model="field.field_value"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label :for="'field_option_'+option"
                                           class="ml-2 text-sm font-medium ">{{
                                            option
                                        }}</label>
                                </div>
                            </div>
                            <div v-if="field.type==='checkboxes'">
                                <div v-for="option in field.options" class="grid grid-cols-1 gap-2">
                                    <jet-label :for="'field_option_'+option">
                                        <div class="flex items-center">
                                            <jet-checkbox :id="'field_option_'+option" :value="option"
                                                          v-model:checked="field.field_value"/>
                                            <div class="ml-2">
                                                {{ option }}
                                            </div>
                                        </div>
                                    </jet-label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                     
                                 <!-- Print Button -->
                   <!-- Print Button -->
                        <jet-button class="ml-4" @click="printForm" :style="{ backgroundColor: 'blue', color: 'white' }">
                            Print
                        </jet-button>
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
                member_id: null,
                group_id: null,
                loan_product_id: null,
                loan_application_checklist_id: null,
                loan_purpose_id: null,
                loan_officer_id: null,
                member_category_id: null,
                member_designation_id: null,
                expected_first_payment_date: null,
                expected_disbursement_date: moment().format("YYYY-MM-DD"),
                submitted_on_date: moment().format("YYYY-MM-DD"),
                applied_amount: null,
                loan_term: null,
                repayment_frequency: null,
                repayment_frequency_type: null,
                interest_rate: null,
                selected_charges: [],
                admin_charges:0.05,
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
            this.form.post(this.route('loans.applications.store'), {})

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
            this.form.loan_officer_id = this.selectedMember.loan_officer_id;
            this.form.member_id = this.selectedMember.id;
            this.form.member_category_id = this.selectedMember.member_category_id;
            this.form.member_designation_id = this.selectedMember.member_designation_id;
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
