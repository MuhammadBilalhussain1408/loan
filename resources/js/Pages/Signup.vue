<template>
    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo/>
        </template>

        <jet-validation-errors class="mb-4"/>

        <form @submit.prevent="submit" class="">
            <div class="grid grid-cols-1 gap-4 mb-4">
                <div>
                    <jet-label for="name" value="Company Name"/>
                    <jet-input id="name" type="text" @blur="updateSubdomain" class="mt-1 block w-full"
                               v-model="form.name" required
                               autofocus/>
                    <jet-input-error :message="form.errors.name" class="mt-2"/>
                </div>
                <div>
                    <jet-label for="name" value="Subdomain"/>
                    <div>
                        <div class="flex rounded-lg shadow-sm">
                            <span
                                class="px-4 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200 bg-gray-50 text-sm text-gray-500">https://</span>
                            <input type="text" name="subdomain"
                                   class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-0 text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                   :class="form.subdomain.length&&!subdomainAvailable?'text-red-600':'text-green-600'"
                                   v-model="form.subdomain" required>
                            <button type="button"
                                    class="flex-shrink-0 inline-flex justify-center items-center gap-x-2 text-sm text-gray-500 font-semibold border rounded-s-0 border-s-0 rounded border-gray-200  bg-gray-50  disabled:opacity-50 disabled:pointer-events-none">

                                <span class="p-2" id="basic-addon2">.{{ central_domain }}</span>
                            </button>
                        </div>
                    </div>
                    <jet-input-error :message="form.errors.subdomain" class="mt-2"/>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                <div>
                    <jet-label for="first_name" value="First Name"/>
                    <jet-input id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" required
                               autocomplete="first_name"/>
                    <jet-input-error :message="form.errors.first_name" class="mt-2"/>
                </div>
                <div>
                    <jet-label for="last_name" value="Last Name"/>
                    <jet-input id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" required
                               autocomplete="surname"/>
                    <jet-input-error :message="form.errors.last_name" class="mt-2"/>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                <div>
                    <jet-label for="country_id" value="Country"/>
                    <select-input id="country_id" class="mt-1 block w-full" v-model="form.country_id" required>
                        <option v-for="item in countries" :value="item.id">{{ item.name }}</option>
                    </select-input>
                    <jet-input-error :message="form.errors.country_id" class="mt-2"/>
                </div>
                <div>
                    <jet-label for="mobile" value="Mobile"/>
                    <div class="flex rounded-lg shadow-sm">
                        <div
                            class="px-4 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200 bg-gray-50">
                            <span class="text-sm text-gray-500 dark:text-gray-400">+{{ form.phone_code }}</span>
                        </div>
                        <input type="text" name="mobile" id="mobile"
                               class="py-3 px-4 pe-11 block w-full border-gray-200 shadow-sm rounded-e-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                               v-model="form.mobile" required>
                    </div>
                    <jet-input-error :message="form.errors.mobile" class="mt-2"/>
                </div>
            </div>

            <div class="mb-4">
                <jet-label for="email" value="Email"/>
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required/>
                <jet-input-error :message="form.errors.name" class="mt-2"/>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                <div>
                    <jet-label for="password" value="Password"/>
                    <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                               autocomplete="new-password"/>
                    <jet-input-error :message="form.errors.name" class="mt-2"/>
                </div>

                <div>
                    <jet-label for="password_confirmation" value="Confirm Password"/>
                    <jet-input id="password_confirmation" type="password" class="mt-1 block w-full"
                               v-model="form.password_confirmation" required autocomplete="new-password"/>
                    <jet-input-error :message="form.errors.name" class="mt-2"/>
                </div>
            </div>
            <div class="mb-4" v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                <jet-label for="terms">
                    <div class="flex items-center">
                        <jet-checkbox name="terms" id="terms" v-model:checked="form.terms"/>

                        <div class="ml-2">
                            I agree to the <a target="_blank" :href="route('admin.terms')"
                                              class="underline text-sm text-gray-600 hover:text-gray-900">Terms of
                            Service</a> and <a target="_blank" :href="route('admin.privacy')"
                                               class="underline text-sm text-gray-600 hover:text-gray-900">Privacy
                            Policy</a>
                        </div>
                    </div>
                </jet-label>
            </div>

            <div class="flex items-center justify-end">
                <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </jet-button>
            </div>
        </form>
    </jet-authentication-card>
    <teleport to="head">
        <title>{{ pageTitle }}</title>
        <meta property="og:description" :content="pageDescription">
    </teleport>
</template>

<script>
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue'
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetCheckbox from "@/Jetstream/Checkbox.vue"
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetInputError from "@/Jetstream/InputError.vue"
import SelectInput from "@/Jetstream/SelectInput.vue"

export default {
    props: {
        countries: Object,
        central_domain: String,
        default_country_id: String,
    },
    components: {
        JetInputError,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetCheckbox,
        SelectInput,
        JetLabel,
        JetValidationErrors
    },

    data() {
        return {
            form: this.$inertia.form({
                first_name: '',
                last_name: '',
                email: '',
                name: '',
                phone_code: '',
                mobile: '',
                country_id: '',
                timezone_id: '',
                subdomain: '',
                password: '',
                password_confirmation: '',
                terms: false,

            }),
            subdomainAvailable: false,
            buttonEnabled: false,
            pageTitle: "Signup",
            pageDescription: "Signup",
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('admin.process_signup'))
        },
        updateSubdomain() {
            this.form.subdomain = this.form.name.replace(/ /g, '').toLocaleLowerCase();

        },
        searchSubdomain() {
            axios.post(route('admin.search_subdomain'), {
                subdomain: this.form.subdomain
            }).then((response) => {
                if (response.data.success === true) {
                    this.buttonEnabled = true;
                    this.subdomainAvailable = true;
                } else {
                    this.buttonEnabled = false;
                    this.subdomainAvailable = false;
                }
            }).catch((error) => {
                this.buttonEnabled = false;
                this.subdomainAvailable = false;

            })
        }
    },
    mounted() {
        this.form.country_id = this.default_country_id
    },
    watch: {
        'form.subdomain': function (newValue, oldValue) {
            this.searchSubdomain();
        },
        'form.country_id': function (newValue, oldValue) {
            this.countries.forEach(item => {
                if (newValue == item.id) {
                    this.form.phone_code = item.phone_code;
                }
            });
        }
    }
}
</script>
