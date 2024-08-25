<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('billing.invoices.index')">
                    Billing
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Create Invoice
            </h2>
        </template>


        <div class=" mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 ">
                        <div>
                            <jet-label for="date" value="Date"/>
                            <flat-pickr
                                v-model="form.date"
                                class="form-control w-full"
                                placeholder="Select date"
                                name="date" required>
                            </flat-pickr>
                            <jet-input-error :message="form.errors.date" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="due_date" value="Due Date"/>
                            <flat-pickr
                                v-model="form.due_date"
                                class="form-control w-full"
                                placeholder="Select date"
                                name="due_date">
                            </flat-pickr>
                            <jet-input-error :message="form.errors.due_date" class="mt-2"/>

                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4">
                        <div>
                            <jet-label for="patient_id" value="Patient"/>
                            <Multiselect
                                v-model="patientsMultiSelect.selected_patient"
                                @select="changePatient"
                                v-bind="patientsMultiSelect"
                                :required="true"
                            />
                        </div>
                        <div>
                            <jet-label for="doctor_id" value="Doctor"/>
                            <Multiselect
                                v-model="form.doctor_id"
                                v-bind="doctorsMultiSelect"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-4 mb-4">
                        <div>
                            <jet-label for="currency_id" value="Currency"/>
                            <Multiselect
                                v-model="form.currency_id"
                                mode="single"
                                @select="changeCurrency"
                                :required="true"
                                :options="$page.props.currencies"
                            />
                            <jet-input-error :message="form.errors.currency_id" class="mt-2"/>
                        </div>
                        <div class="">
                            <jet-label for="xrate" value="Exchange Rate"/>
                            <jet-input id="xrate" :readonly="invoiceAllowEditingExchangeRate==='no'" type="text"
                                       class="block w-full" @input="changeExchangeRate" v-model="form.xrate"/>
                            <jet-input-error :message="form.errors.xrate" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="sponsor" value="Sponsor"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="gender" v-model="form.sponsor" id="sponsor" required>
                                <option value="cash">Cash</option>
                                <option value="co_payer">CoPayer</option>
                            </select>
                            <jet-input-error :message="form.errors.sponsor" class="mt-2"/>
                        </div>
                    </div>
                    <div v-if="form.sponsor==='co_payer'">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <jet-label for="co_payer_id" value="CoPayer"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                    name="co_payer_id" v-model="form.co_payer_id" id="co_payer_id" required>
                                    <option v-for="item in $page.props.coPayers" :value="item.value">
                                        {{ item.label }}
                                    </option>
                                </select>
                                <jet-input-error :message="form.errors.co_payer_id" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="co_payer_member_name" value="Member Name"/>
                                <jet-input type="text" id="co_payer_member_name" class="block w-full"
                                           v-model="form.co_payer_member_name" required/>
                                <jet-input-error :message="form.errors.co_payer_member_name"
                                                 class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="co_payer_patient_relationship_id"
                                           value="Relationship To Member"/>
                                <select
                                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                    name="co_payer_patient_relationship_id"
                                    v-model="form.co_payer_patient_relationship_id"
                                    id="co_payer_patient_relationship_id" required>
                                    <option v-for="item in $page.props.patientRelationships"
                                            :value="item.value">{{ item.label }}
                                    </option>
                                </select>
                                <jet-input-error :message="form.errors.co_payer_patient_relationship_id"
                                                 class="mt-2"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <jet-label for="co_payer_membership_number" value="Membership Number"/>
                                <jet-input type="text" id="co_payer_membership_number"
                                           class="block w-full"
                                           v-model="form.co_payer_membership_number" required/>
                                <jet-input-error :message="form.errors.co_payer_membership_number"
                                                 class="mt-2"/>

                            </div>
                            <div>
                                <jet-label for="co_payer_suffix" value="Suffix"/>
                                <jet-input type="text" id="co_payer_suffix" class=" block w-full"
                                           v-model="form.co_payer_suffix" required/>
                                <jet-input-error :message="form.errors.co_payer_suffix"
                                                 class="mt-2"/>

                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-4 mb-4">
                        <div>
                            <jet-label for="tariff" value="Tariff"/>
                            <Multiselect
                                v-model="selectedTariff"
                                v-bind="tariffsMultiSelect"
                                :object="true"
                                @select="addItem"
                                ref="tariffsMultiselectField"
                            />
                        </div>
                    </div>
                    <div class="mt-4 mb-4 overflow-x-scroll">
                        <table class="w-full whitespace-no-wrap">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Name</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Qty</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Unit Cost</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Tax</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500" v-if="form.sponsor==='co_payer'">
                                    Cash
                                </th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500" v-if="form.sponsor==='co_payer'">
                                    CoPayer
                                </th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Total</th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item,index) in form.items">
                                <td class="border-t">
                                    <jet-input type="text" class="mt-1 block w-full"
                                               v-model="item.name"
                                               required autocomplete="off"/>
                                </td>
                                <td class="border-t">
                                    <jet-input type="text" class="mt-1 block w-full"
                                               v-model="item.qty"
                                               required autocomplete="off" @input="updateItems"/>
                                </td>
                                <td class="border-t">
                                    <jet-input type="text" class="mt-1 block w-full"
                                               v-model="item.unit_cost"
                                               required autocomplete="off" @input="updateItems"/>
                                </td>
                                <td class="border-t">
                                    <select @change="updateItems"
                                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                            name="tax_rate_id" v-model="item.tax_rate_id"
                                            id="tax_rate_id">
                                        <option v-for="tax_rate in taxRates" :value="tax_rate.id">{{
                                                tax_rate.name
                                            }}
                                        </option>
                                    </select>
                                </td>
                                <td class="border-t" v-if="form.sponsor==='co_payer'">
                                    <jet-input type="text" class="mt-1 block w-full"
                                               v-model="item.cash_amount" autocomplete="off"
                                               @input="item.co_payer_amount=item.total-item.cash_amount;updateItems"/>
                                </td>
                                <td class="border-t" v-if="form.sponsor==='co_payer'">
                                    <jet-input type="text" class="mt-1 block w-full"
                                               v-model="item.co_payer_amount" autocomplete="off"
                                               @input="item.cash_amount=item.total-item.co_payer_amount;updateItems"/>
                                </td>
                                <td class="border-t">
                                    <jet-input type="text" class="mt-1 block w-full"
                                               v-model="item.total"
                                               required autocomplete="off" readonly/>
                                </td>
                                <td class="border-t  flex items-center justify-center">
                                    <div class="text-red-400 mt-2">
                                        <font-awesome-icon icon="trash" v-on:click="removeItem(index)"
                                                           class="w-4 h-4 mr-2"></font-awesome-icon>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="4"></th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500" v-if="form.sponsor==='co_payer'">
                                    {{ form.cash_amount }}
                                </th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500" v-if="form.sponsor==='co_payer'">
                                    {{ form.co_payer_amount }}
                                </th>
                                <th class="px-6 pt-4 pb-4 font-medium text-gray-500">{{ form.amount }}</th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="grid grid-cols-1 gap-2">
                        <div>
                            <jet-label for="terms" value="Terms"/>
                            <textarea-input id="terms" class="mt-1 block w-full"
                                            v-model="form.terms"/>
                            <jet-input-error :message="form.errors.terms" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="client_notes" value="Client Notes"/>
                            <textarea-input id="client_notes" class="mt-1 block w-full"
                                            v-model="form.client_notes"/>
                            <jet-input-error :message="form.errors.client_notes" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="admin_notes" value="Admin Notes"/>
                            <textarea-input id="admin_notes" class="mt-1 block w-full"
                                            v-model="form.admin_notes"/>
                            <jet-input-error :message="form.errors.admin_notes" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="description" value="Description"/>
                            <textarea-input id="description" class="mt-1 block w-full"
                                            v-model="form.description"/>
                            <jet-input-error :message="form.errors.description" class="mt-2"/>

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
const fetchDoctors = async (query) => {
    let where = ''
    const response = await fetch(
        route('users.search') + '?type=doctor&s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name + (item.practice_number ?? '')}
    })
}
const fetchPatients = async (query) => {
    let where = ''
    const response = await fetch(
        route('patients.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return item;
    })
}
const fetchTariffs = async (query) => {
    let where = ''
    const response = await fetch(
        route('tariffs.search') + '?s=' + query,
        {}
    );

    const data = await response.json(); // Here you have the data that you need
    return data.map((item) => {
        return {value: item.id, label: item.name, item: item}
    })
}
import AppLayout from '@/Layouts/AppLayout.vue'
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import Select from "@/Jetstream/Select.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";


export default {
    props: {
        branches: Object,
        patientRelationships: Object,
        defaultTariffs: Object,
        currencies: Object,
        taxRates: Object,
        coPayers: Object,
        invoiceAllowEditingExchangeRate: String,
        invoiceTerms: String,
        invoiceDueAfterDays: String,
    },
    components: {
        Select,
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
                patient_id: null,
                doctor_id: null,
                consultation_id: null,
                co_payer_id: null,
                patient_co_payer_id: null,
                co_payer_membership_number: null,
                co_payer_patient_relationship_id: null,
                co_payer_member_name: null,
                co_payer_suffix: null,
                co_payer_cover: null,
                currency_id: this.$page.props.currency.id,
                sponsor: 'cash',
                reference: null,
                date: moment().format("YYYY-MM-DD"),
                due_date: null,
                description: null,
                client_notes: null,
                terms: this.invoiceTerms,
                admin_notes: null,
                xrate: 1,
                tax_total: 0,
                amount: 0,
                cash_amount: 0,
                co_payer_amount: 0,
                items: [],
            }),
            doctorsMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Doctor',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchDoctors(query)
                }
            },
            patientsMultiSelect: {
                valueProp: 'id',
                label: 'name',
                selected_patient: null,
                placeholder: 'Search for Patient',
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
            coPayersMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for CoPayer',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: []
            },
            tariffsMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Tariff',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchTariffs(query)
                }
            },
            selectedTariff: null,
            currency: this.$page.props.currency,
            pageTitle: "Create Invoice",
            pageDescription: "Create Invoice",
        }

    },
    mounted() {

    },
    methods: {
        submit() {
            this.form.post(this.route('billing.invoices.store'), {})

        },
        addItem() {
            if (this.currency === null) {
                alert('Please set currency first');
                return;
            }
            if (this.selectedTariff) {
                let existing = false;
                this.form.items.forEach(item => {
                    if (item.tariff_id === this.selectedTariff.item.id) {
                        existing = true;
                        item.qty++;
                    }
                });
                if (!existing) {
                    this.form.items.push({
                        tariff_id: this.selectedTariff.item.id,
                        qty: 1,
                        tax_rate_id: null,
                        name: this.selectedTariff.item.name,
                        cash_amount: parseFloat(this.selectedTariff.item.cash_amount || 0),
                        base_currency_cash_amount: parseFloat(this.selectedTariff.item.cash_amount || 0),
                        co_payer_amount: parseFloat(this.selectedTariff.item.co_payer_amount || 0),
                        base_currency_co_payer_amount: parseFloat(this.selectedTariff.item.co_payer_amount || 0),
                        co_payer_percent: parseFloat(this.selectedTariff.item.co_payer_percent || 0),
                        unit_cost: parseFloat(this.selectedTariff.item.amount || 0),
                        base_currency_unit_cost: parseFloat(this.selectedTariff.item.amount || 0),
                        tax_total: 0,
                        total: parseFloat(this.selectedTariff.item.amount || 0),
                        maximum_quantity: this.selectedTariff.item.maximum_quantity,
                        type: this.selectedTariff.item.type,
                        is_claimable: this.selectedTariff.item.is_claimable,
                        needs_approval: this.selectedTariff.item.needs_approval,
                    })
                }
                this.updateItems();
            }
            this.$refs.tariffsMultiselectField.clear()
            this.$refs.tariffsMultiselectField.clearSearch()
            this.$refs.tariffsMultiselectField.refreshOptions()
            this.$refs.tariffsMultiselectField.deselect()
        },
        updateItems() {
            this.form.amount = 0;
            this.form.tax_total = 0;
            this.form.cash_amount = 0;
            this.form.co_payer_amount = 0;
            this.form.items.forEach(item => {
                if (item.tax_rate_id) {
                    this.taxRates.forEach(taxRate => {
                        if (taxRate.id === item.tax_rate_id) {
                            if (taxRate.type === 'fixed') {
                                item.tax_total = parseFloat(taxRate.amount || 0);
                            } else {
                                item.tax_total = parseFloat((item.unit_cost * item.qty * parseFloat(taxRate.amount || 0)) / 100);
                            }
                        }
                    })
                }
                item.total = parseFloat(item.tax_total) + (parseFloat(item.unit_cost || 0) * (item.qty || 0));
                if(parseFloat(item.co_payer_amount)===0){
                    item.cash_amount=item.total;
                }
                if(this.form.sponsor==='cash'){
                    item.cash_amount = parseFloat(item.total);
                    item.co_payer_amount = 0;
                }
                this.form.amount += parseFloat(item.total);
                this.form.tax_total += parseFloat(item.tax_total);
                this.form.co_payer_amount += parseFloat(item.co_payer_amount);
                this.form.cash_amount += parseFloat(item.cash_amount);

            });
        },
        removeItem(index) {
            this.form.items.splice(index, 1);
            this.updateItems();
        },
        changePatient() {
            this.form.patient_id = this.patientsMultiSelect.selected_patient.id;
            this.coPayersMultiSelect.options = [];
            if (this.patientsMultiSelect.selected_patient && this.patientsMultiSelect.selected_patient.co_payers !== null) {
                this.patientsMultiSelect.selected_patient.co_payers.forEach(item => {
                    if (this.patientsMultiSelect.selected_patient.default_co_payer_id === item.id) {
                        this.form.form = item.id;
                    }
                    this.coPayersMultiSelect.options.push({label: item.co_payer.name, value: item.id})
                })
            }
        },
        changeCurrency() {
            this.currencies.forEach(item => {
                if (item.value === this.form.currency_id) {
                    this.currency = item;
                    this.form.xrate = parseFloat(item.xrate || 1);
                }
            });
            this.form.items.forEach(item => {
                item.unit_cost = parseFloat(item.base_currency_unit_cost || 0) * this.form.xrate;
                item.cash_amount = parseFloat(item.base_currency_cash_amount || 0) * this.form.xrate;
                item.co_payer_amount = parseFloat(item.base_currency_co_payer_amount || 0) * this.form.xrate;

            });
            this.updateItems();
        },
        changeExchangeRate() {
            this.form.items.forEach(item => {
                item.unit_cost = parseFloat(item.base_currency_unit_cost || 0) * this.form.xrate;
                item.cash_amount = parseFloat(item.base_currency_cash_amount || 0) * this.form.xrate;
                item.co_payer_amount = parseFloat(item.base_currency_co_payer_amount || 0) * this.form.xrate;
            });
            this.updateItems();
        }
    },
    watch: {
        'form.co_payer_id': function (val) {
            if (this.patientsMultiSelect.selected_patient && this.patientsMultiSelect.selected_patient.co_payers !== null) {
                this.patientsMultiSelect.selected_patient.co_payers.forEach(item => {
                    if (item.co_payer_id === val) {
                        this.form.co_payer_membership_number = item.membership_number;
                        this.form.co_payer_member_name = item.member_name;
                        this.form.co_payer_suffix = item.suffix;
                        this.form.co_payer_patient_relationship_id = item.patient_relationship_id;
                    }
                })
            }

        }
    }
}
</script>
<style scoped>

</style>
