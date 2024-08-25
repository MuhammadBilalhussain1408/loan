<template>
    <div>
        <div class="flex justify-center">
            <img v-if="member.profile_photo_url" :src="member.profile_photo_url" alt=""
                 class="rounded-full mx-auto absolute -top-20 w-32 h-32 shadow-2xl border-4 border-white">
        </div>
        <div class="mt-16">
            <h1 class="font-bold text-center text-xl text-gray-900">
                <span v-if="member.title" class="">{{ member.title.name }}</span> {{ member.name }}
            </h1>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ member.gender }}
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ member.age }}
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ member.mobile }}
            </p>
            <p class="text-center text-sm text-gray-400 font-medium">
                {{ member.email }}
            </p>
        </div>
        <div class="w-full mt-5">
            <inertia-link
                class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                :class="{'bg-gray-100': route().current('portal.member.show')}"
                :href="route('portal.member.show')">
                <font-awesome-icon icon="user" class="w-4 h-4 mr-2"></font-awesome-icon>
                Basic Profile
            </inertia-link>
            <inertia-link
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.beneficiaries.index')}"
                          :href="route('portal.member.beneficiaries.index')">
                <font-awesome-icon icon="users" class="w-4 h-4 mr-2"></font-awesome-icon>
                Beneficiaries
            </inertia-link>
            <inertia-link
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('portal.member.files.index')}"
                          :href="route('portal.member.files.index')">
                <font-awesome-icon icon="folder" class="w-4 h-4 mr-2"></font-awesome-icon>
                Files
            </inertia-link>
        </div>
    </div>
</template>

<script>
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetSuccessButton from '@/Jetstream/SuccessButton.vue'
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SelectInput from "@/Jetstream/SelectInput.vue";

export default {
    components: {
        JetDialogModal,
        JetInputError,
        JetLabel,
        SelectInput,
        JetConfirmationModal,
        JetDangerButton,
        JetSuccessButton,
        JetSecondaryButton,
    },
    props: {
        member: Object,
    },
    data() {
        return {
            processing: false,
            confirmingMemberDeletion: false,
            showChangeStatusModal: false,

        }
    },
    methods: {

    },
}
</script>

<style scoped>

</style>
