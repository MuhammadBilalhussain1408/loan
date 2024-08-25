<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('members.index')">Members
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span> Edit
            </h2>
        </template>
        <div class="mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">

                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <div>
                            <jet-label for="title_id" value="Title"/>
                            <Multiselect
                                id="title_id"
                                v-model="form.title_id"
                                value-prop="id"
                                label="name"
                                :options="titles"
                            />
                            <jet-input-error :message="form.errors.title_id" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="first_name" value="First Name"/>
                            <jet-input id="first_name" type="text" class="block w-full"
                                       v-model="form.first_name"
                                       required
                                       autofocus autocomplete="first_name"/>
                            <jet-input-error :message="form.errors.first_name" class="mt-2"/>
                        </div>
                        <div class="">
                            <jet-label for="middle_name" value="Middle Name"/>
                            <jet-input id="middle_name" type="text" class=" block w-full"
                                       v-model="form.middle_name"
                                       autocomplete="middle_name"/>
                            <jet-input-error :message="form.errors.middle_name" class="mt-2"/>
                        </div>
                        <div class="">
                            <jet-label for="last_name" value="Last Name"/>
                            <jet-input id="last_name" type="text" class="block w-full" v-model="form.last_name"
                                       required autocomplete="last_name"/>
                            <jet-input-error :message="form.errors.last_name" class="mt-2"/>
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
                    <div class="grid grid-cols-1 md:grid-cols-3 mt-4 gap-2">
                        <div class="">
                            <jet-label for="identification_number" value="Identification Number"/>
                            <jet-input id="identification_number" type="text" class="block w-full"
                                       v-model="form.identification_number"
                                       required autocomplete="identification_number"/>
                            <jet-input-error :message="form.errors.identification_number" class="mt-2"/>
                        </div>
                        <div class="">
                            <jet-label for="graded_tax_number" value="Graded Tax Number"/>
                            <jet-input id="graded_tax_number" type="text" class="block w-full"
                                       v-model="form.graded_tax_number"
                            />
                            <jet-input-error :message="form.errors.graded_tax_number" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="dob" value="Date of Birth"/>
                            <flat-pickr
                                v-model="form.dob"
                                class="form-control w-full"
                                placeholder="Select date"
                                required
                                id="dob"
                                name="dob">
                            </flat-pickr>
                            <jet-input-error :message="form.errors.dob" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="gender" value="Gender"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="gender" v-model="form.gender" id="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <jet-input-error :message="form.errors.gender" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="marital_status" value="Marital Status"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="marital_status" v-model="form.marital_status" id="marital_status">
                                <option :value="null"/>
                                <option value="married">Married</option>
                                <option value="single">Single</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                                <option value="other">Other</option>
                            </select>
                            <jet-input-error :message="form.errors.marital_status" class="mt-2"/>
                        </div>

                        <div>
                            <jet-label for="contact_number" value="Contact Number"/>
                            <jet-input id="contact_number" type="text" class="block w-full"
                                       v-model="form.contact_number"/>
                            <jet-input-error :message="form.errors.contact_number" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="home_number" value="Home Number"/>
                            <jet-input id="home_number" type="text" class="block w-full" v-model="form.home_number"/>
                            <jet-input-error :message="form.errors.home_number" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="email" value="Email"/>
                            <jet-input id="email" type="email" class="block w-full" v-model="form.email"/>
                            <jet-input-error :message="form.errors.email" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="number_of_spouses" value="Number of spouses"/>
                            <jet-input id="number_of_spouses" type="number" class="block w-full" v-model="form.number_of_spouses"/>
                            <jet-input-error :message="form.errors.number_of_spouses" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="number_of_children" value="Number of children"/>
                            <jet-input id="number_of_children" type="number" class="block w-full" v-model="form.number_of_children"/>
                            <jet-input-error :message="form.errors.number_of_children" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="date_of_appointment" value="Date of appointment"/>
                            <flat-pickr
                                v-model="form.date_of_appointment"
                                class="form-control w-full"
                                placeholder="Select date"
                                required
                                id="date_of_appointment"
                                name="date_of_appointment">
                            </flat-pickr>
                            <jet-input-error :message="form.errors.date_of_appointment" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="term_end_date" value="Term end date"/>
                            <flat-pickr
                                v-model="form.term_end_date"
                                class="form-control w-full"
                                placeholder="Select date"
                                required
                                id="term_end_date"
                                name="term_end_date">
                            </flat-pickr>
                            <jet-input-error :message="form.errors.term_end_date" class="mt-2"/>

                        </div>
                        <div>
                            <jet-label for="monthly_or_annual_salary" value="Monthly/Annual Salary"/>
                            <jet-input id="monthly_or_annual_salary" type="text" class="block w-full" v-model="form.monthly_or_annual_salary"/>
                            <jet-input-error :message="form.errors.monthly_or_annual_salary" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="postal_address" value="Postal Address"/>
                            <textarea-input id="postal_address" class=" block w-full"
                                            v-model="form.postal_address"/>
                            <jet-input-error :message="form.errors.postal_address" class="mt-2"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mt-4">
                        <div>
                            <jet-label for="english">
                                <div class="flex items-center">
                                    <jet-checkbox name="english" id="english"
                                                  v-model:checked="form.english"/>
                                    <div class="ml-2">
                                        English
                                    </div>
                                </div>
                            </jet-label>
                        </div>
                        <div>
                            <jet-label for="eswatini">
                                <div class="flex items-center">
                                    <jet-checkbox name="eswatini" id="eswatini"
                                                  v-model:checked="form.eswatini"/>
                                    <div class="ml-2">
                                        Eswatini
                                    </div>
                                </div>
                            </jet-label>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 mt-4 gap-2">
                        <div>
                            <jet-label for="photo" value="Photo"/>
                            <file-input v-model="form.photo" class="block w-full" id="photo" type="file"/>
                            <jet-input-error :message="form.errors.photo" class="mt-2"/>
                        </div>
                        <div>
                            <jet-label for="status" value="Status"/>
                            <select
                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                                name="status" v-model="form.status" id="status" required>
                                <option :value="null"/>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="deceased">Deceased</option>
                                <option value="closed">Closed</option>
                            </select>
                            <jet-input-error :message="form.errors.status" class="mt-2"/>
                        </div>
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
                    <div class="mt-4">
                        <jet-label for="description" value="Extra Notes"/>
                        <textarea-input id="description" class=" block w-full"
                                        v-model="form.description"/>
                        <jet-input-error :message="form.errors.description" class="mt-2"/>
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

export default {
    props: {
        member: Object,
        countries: Object,
        titles: Object,
        professions: Object,
        branches: Object,
        customFields: Object,
        categories: Object,
        designations: Object,
    },
    components: {
        AppLayout,
        SelectInput,
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
                '_method': 'PUT',
                first_name: this.member.first_name,
                middle_name: this.member.middle_name,
                last_name: this.member.last_name,
                gender: this.member.gender,
                email: this.member.email,
                country_id: this.member.country_id,
                branch_id: this.member.branch_id,
                loan_officer_id: this.member.loan_officer_id,
                state: this.member.state,
                city: this.member.city,
                title_id: this.member.title_id,
                member_category_id: this.member.member_category_id,
                member_designation_id: this.member.member_designation_id,
                mobile: this.member.mobile,
                profession_id: this.member.profession_id,
                graded_tax_number: this.member.graded_tax_number,
                zip: this.member.zip,
                external_id: this.member.external_id,
                contact_number: this.member.contact_number,
                home_number: this.member.home_number,
                address: this.member.address,
                employer: this.member.employer,
                dob: this.member.dob,
                marital_status: this.member.marital_status,
                identification_number: this.member.identification_number,
                number_of_spouses: this.member.number_of_spouses,
                number_of_children: this.member.number_of_children,
                status: this.member.status,
                english: this.member.english,
                eswatini: this.member.eswatini,
                other_language: this.member.other_language,
                monthly_or_annual_salary: this.member.monthly_or_annual_salary,
                date_of_appointment: this.member.date_of_appointment,
                term_end_date: this.member.term_end_date,
                postal_address: this.member.postal_address,
                photo: null,
                custom_fields: this.member.custom_fields
            }),
            usersMultiSelect: {
                value: null,
                remark: null,
                placeholder: 'Search for Staff',
                filterResults: false,
                minChars: 2,
                resolveOnLoad: false,
                delay: 4,
                searchable: true,
                options: async (query) => {
                    return await fetchUsers(query)
                }
            },
            pageTitle: "Edit Member",
            pageDescription: "Edit Member",
        }

    },
    methods: {
        submit() {
            this.form.post(this.route('portal.member.update'), {})
        },

    }
}
</script>
<style scoped>

</style>
