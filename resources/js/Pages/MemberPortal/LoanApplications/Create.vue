<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600"
                              :href="route('portal.loans.applications.index')">
                    Loan Applications
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Apply
            </h2>
        </template>


        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1  gap-4 ">
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
                        <div class="" v-if="selectedProduct">
                            <jet-label for="applied_amount" value="Amount"/>
                            <jet-input id="applied_amount" type="number" :min="selectedProduct.minimum_principal"
                                       :max="selectedProduct.maximum_principal"
                                       class="block w-full" v-model="form.applied_amount" required/>
                            <jet-input-error :message="form.errors.applied_amount" class="mt-2"/>
                        </div>
                        <div class="" v-if="selectedProduct">
                            <jet-label for="description" value="Additional Notes"/>
                            <textarea-input id="description"
                                            class="block w-full" v-model="form.description"/>
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


import AppLayout from '@/Pages/MemberPortal/Layouts/AppLayout.vue'
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
                loan_product_id: null,
                loan_purpose_id: null,
                applied_amount: null,
                description: null,
                custom_fields: this.customFields,
            }),

            selectedMember: null,
            selectedProduct: null,
            selectedCharge: null,
            pageTitle: "Apply for a Loan",
            pageDescription: "Apply for a Loan",
        }

    },
    mounted() {

    },
    methods: {
        submit() {
            this.form.post(this.route('portal.loans.applications.store'), {})

        },
        changeLoanProduct() {
            if (this.selectedProduct) {
                this.form.loan_product_id = this.selectedProduct.id;
                this.form.applied_amount = this.selectedProduct.default_principal;
            }
        },
    },
    computed: {},
    watch: {}
}
</script>
<style scoped>

</style>
