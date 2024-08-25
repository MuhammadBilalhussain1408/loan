<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.index')">Loans
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('loans.show',loan.id)">Loan
                    #{{ loan.id }}
                </inertia-link>
                <span class="text-indigo-400 font-medium">/</span>
                Edit Loan Schedule
            </h2>
        </template>
        <div class=" mx-auto">
            <div class="bg-white shadow-xl sm:rounded-lg p-4">
                <form @submit.prevent="submit">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-no-wrap">
                            <thead class="bg-gray-50">
                            <tr class="text-left font-bold">
                                <th class="p-4  font-medium text-gray-500">#</th>
                                <th class="p-4 font-medium text-gray-500">Due Date</th>
                                <th class="p-4 font-medium text-gray-500">Principal</th>
                                <th class="p-4 font-medium text-gray-500">Interest</th>
                                <th class="p-4 font-medium text-gray-500">Fees</th>
                                <th class="p-4  font-medium text-gray-500">Penalty</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item,index) in form.schedules" :key="item.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100">
                                <td class="border-t p-4">
                                    {{ index+1 }}
                                </td>
                                <td class="border-t p-4">
                                    <flat-pickr
                                        v-model="item.due_date"
                                        class="form-control w-full"
                                        placeholder="Select date">
                                    </flat-pickr>
                                </td>
                                <td class="border-t p-4">
                                    <jet-input type="text" class=" block w-full"
                                               v-model="item.principal"/>
                                </td>
                                <td class="border-t p-4">
                                    <jet-input type="text" class=" block w-full"
                                               v-model="item.interest"/>
                                </td>
                                <td class="border-t p-4">
                                    <jet-input type="text" class=" block w-full"
                                               v-model="item.fees"/>
                                </td>
                                <td class="border-t p-4">
                                    <jet-input type="text" class=" block w-full"
                                               v-model="item.penalties"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
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
import AppLayout from '@/Layouts/AppLayout.vue'
import JetLabel from '@/Jetstream/Label.vue'
import SelectInput from '@/Jetstream/SelectInput.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import FileInput from "@/Jetstream/FileInput.vue";
import TextareaInput from "@/Jetstream/TextareaInput.vue";

export default {
    components: {
        AppLayout,
        TextareaInput,
        JetLabel,
        JetInput,
        JetInputError,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        JetButton,
        JetCheckbox,
        FileInput,
    },
    props: {
        loan: Object,

    },
    data() {
        return {
            form: this.$inertia.form({
                schedules: this.loan.schedules,
            }),
            pageTitle: "Edit Loan Schedule",
            pageDescription: "Edit Loan Schedule",

        }
    },

    methods: {
        submit() {
            this.form.put(this.route('loans.schedules.update', this.loan.id), {})
        },
    },
}
</script>

<style scoped>

</style>
