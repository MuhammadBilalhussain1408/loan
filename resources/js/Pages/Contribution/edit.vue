<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('contribution.index')">
                    Contribution
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Edit
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 ">
                        <div>
                            <jet-label for="member_id" value="Member" />
                            <Multiselect v-model="selectedMember" @select="changeMember" v-bind="membersMultiSelect" />
                        </div>
                    </div>
                    <div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div>
                                <jet-label for="name" value="Name" />
                                <jet-input id="name" type="text" class="block w-full" v-model="form.name" />
                                <jet-input-error :message="form.errors.name" class="mt-2" />
                            </div>
                            <div>
                                <jet-label for="surname" value="Surname" />
                                <jet-input id="surname" type="text" class="block w-full" v-model="form.surname" />
                                <jet-input-error :message="form.errors.surname" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">

                            <div>
                                <jet-label for="member_category" value="Member Category" />
                                <jet-input id="member_category" type="text" class="block w-full"
                                    v-model="form.member_category" />
                                <jet-input-error :message="form.errors.member_category" class="mt-2" />
                            </div>
                            <div>
                                <jet-label for="gender" value="Gender" />
                                <jet-input id="gender" type="text" class="block w-full" v-model="form.gender" />
                                <jet-input-error :message="form.errors.gender" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">

                            <div>
                                <jet-label for="id_no" value="Id No" />
                                <jet-input id="id_no" type="text" class="block w-full" v-model="form.id_no" />
                                <jet-input-error :message="form.errors.id_no" class="mt-2" />
                            </div>
                            <div>
                                <jet-label for="basic_salary" value="Basic Salary" />
                                <jet-input id="basic_salary" type="text" class="block w-full"
                                    v-model="form.basic_salary" />
                                <jet-input-error :message="form.errors.basic_salary" class="mt-2" />
                            </div>

                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                            <div>
                                <jet-label for="contri_15_per" value="15% Employee Contrbution" />
                                <jet-input id="contri_15_per" type="text" class="block w-full"
                                    v-model="form.contri_15_per" />
                                <jet-input-error :message="form.errors.contri_15_per" class="mt-2" />
                            </div>

                            <div>
                                <jet-label for="contri_30_per" value="30% Employer Contrbution" />
                                <jet-input id="contri_30_per" type="text" class="block w-full"
                                    v-model="form.contri_30_per" />
                                <jet-input-error :message="form.errors.contri_30_per" class="mt-2" />
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">

                        <!-- Print Button -->
                        <!-- Print Button -->
                        <!-- <jet-button class="ml-4" @click="printForm" :style="{ backgroundColor: 'blue', color: 'white' }">
                            Print
                        </jet-button> -->
                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
        memberContribution: Object
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
                member_id: this.memberContribution.member_id,
                name: this.memberContribution.name,
                surname: this.memberContribution.Surname,
                member_category: this.memberContribution.member_category,
                gender: this.memberContribution.gender,
                id_no: this.memberContribution.id_no,
                basic_salary: this.memberContribution.basic_salary,
                contri_15_per: this.memberContribution.contri_15_per,
                contri_30_per: this.memberContribution.contri_30_per,
                reference: this.memberContribution.reference,
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
            pageTitle: "Create Contribution",
            pageDescription: "Create Contribution",
        }

    },
    mounted() {

    },
    methods: {
        submit() {
            this.form.put(this.route('contribution.update', this.memberContribution.id), {})

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
            this.form.member_account_holder = this.selectedMember.first_name + ' ' + this.selectedMember.middle_name + ' ' + this.selectedMember.last_name;
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
<style scoped></style>
