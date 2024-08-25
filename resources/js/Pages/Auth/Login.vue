<template>
    <div class="flex min-h-screen overflow-hidden">
        <div class="relative hidden w-0 flex-1 lg:block">
            <img class="absolute inset-0 h-full w-full object-cover"
                 src="/images/login.jpg"
                 alt="">
        </div>
        <div class="flex flex-1 flex-col justify-center  px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden">
                        <jet-authentication-card-logo/>
                    </div>
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Sign in to your account</h2>
                </div>

                <div class="mt-8">
                    <div class="mt-6">
                        <jet-validation-errors class="mb-4"/>
                        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                            {{ status }}
                        </div>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <jet-label for="email" value="Email address"/>
                                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email"
                                           required autofocus/>
                            </div>
                            <div class="mt-4">
                                <jet-label for="password" value="Password"/>
                                <jet-input id="password" type="password" class="mt-1 block w-full"
                                           v-model="form.password" required
                                           autocomplete="current-password"/>
                            </div>

                            <div class="flex items-center justify-between">

                                <label class="flex items-center">
                                    <jet-checkbox name="remember" v-model:checked="form.remember"/>
                                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                </label>
                                <inertia-link v-if="canResetPassword" :href="route('password.request')"
                                              class="underline text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    Forgot your password?
                                </inertia-link>
                            </div>

                            <div>
                                <button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                        class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Sign in
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <teleport to="head">
        <title>{{ pageTitle }}</title>
        <meta property="og:description" :content="pageDescription">
    </teleport>
</template>

<script>
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'

export default {
    components: {
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetValidationErrors
    },

    props: {
        canResetPassword: Boolean,
        status: String
    },

    data() {
        return {
            form: this.$inertia.form({
                email: '',
                password: '',
                remember: false
            }),
            pageTitle: "Login",
            pageDescription: "Login",
        }
    },

    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ...data,
                    remember: this.form.remember ? 'on' : ''
                }))
                .post(this.route('login'), {
                    onFinish: () => this.form.reset('password'),
                })
        }
    }
}
</script>
