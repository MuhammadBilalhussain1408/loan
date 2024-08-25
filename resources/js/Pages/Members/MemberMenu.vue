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
            <div class="flex justify-center">
                <inertia-link v-if="can('communication.campaigns.create')"
                              :href="route('communication.campaigns.create',{member_id:member.id,campaign_type:'sms'})"
                              class="btn btn-success  mr-2" title="SMS">
                    <font-awesome-icon icon="sms" class="w-4 h-4"></font-awesome-icon>
                </inertia-link>
                <inertia-link v-if="can('communication.campaigns.create')"
                              :href="route('communication.campaigns.create',{member_id:member.id,campaign_type:'email'})"
                              class="btn btn-success" title="Email">
                    <font-awesome-icon icon="envelope" class="w-4 h-4"></font-awesome-icon>
                </inertia-link>
            </div>
        </div>
        <div class="w-full mt-5">
            <inertia-link
                class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                :class="{'bg-gray-100': route().current('members.show')}"
                :href="route('members.show',member.id)">
                <font-awesome-icon icon="user" class="w-4 h-4 mr-2"></font-awesome-icon>
                Basic Profile
            </inertia-link>
            <inertia-link v-if="can('loans.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.other_loan.index')}"
                          :href="route('members.other_loan.index',member.id)">
                <font-awesome-icon icon="database" class="w-4 h-4 mr-2"></font-awesome-icon>
                Other Loans
            </inertia-link>
            <inertia-link v-if="can('loans.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.loans.index')}"
                          :href="route('members.loans.index',member.id)">
                <font-awesome-icon icon="database" class="w-4 h-4 mr-2"></font-awesome-icon>
                Loans
            </inertia-link>

            <inertia-link v-if="can('loans.applications.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.applications.index')}"
                          :href="route('members.applications.index',member.id)">
                <font-awesome-icon icon="university" class="w-4 h-4 mr-2"></font-awesome-icon>
                Loan Applications
            </inertia-link>
            <inertia-link v-if="can('members.beneficiaries.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.beneficiaries.index')}"
                          :href="route('members.beneficiaries.index',member.id)">
                <font-awesome-icon icon="users" class="w-4 h-4 mr-2"></font-awesome-icon>
                Beneficiaries
            </inertia-link>
            <inertia-link v-if="can('members.files.index')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.files.index')}"
                          :href="route('members.files.index',member.id)">
                <font-awesome-icon icon="folder" class="w-4 h-4 mr-2"></font-awesome-icon>
                Files
            </inertia-link>
            <inertia-link v-if="can('members.create')"
                          class="w-full border-t border-gray-100 font-medium text-gray-600 py-2 px-4  block hover:bg-gray-100 transition duration-150"
                          :class="{'bg-gray-100': route().current('members.login_details.index')}"
                          :href="route('members.login_details.index',member.id)">
                <font-awesome-icon icon="user-lock" class="w-4 h-4 mr-2"></font-awesome-icon>
                Login Details
            </inertia-link>
        </div>
        <div class="p-5 border-t flex justify-between">
            <button v-if="can('members.update')" @click="showChangeStatusModal=true"
                          class="btn btn-success py-1 px-2">Change Status
            </button>
            <inertia-link v-if="can('members.update')" :href="route('members.edit',member.id)"
                          class="btn btn-primary py-1 ">Edit
            </inertia-link>
            <button type="button" v-if="can('members.destroy')" @click="deleteAction"
                    class="btn btn-danger py-1 px-2">Delete
            </button>
        </div>
    </div>
    <jet-confirmation-modal :show="confirmingMemberDeletion" @close="confirmingMemberDeletion = false">
        <template #title>
            Delete Record
        </template>

        <template #content>
            Are you sure you want to delete record? Once the record is deleted, all of its resources and
            data will be permanently deleted.
        </template>

        <template #footer>
            <jet-secondary-button @click.native="confirmingMemberDeletion = false">
                Nevermind
            </jet-secondary-button>

            <jet-danger-button class="ml-2" @click.native="destroy" :class="{ 'opacity-25': processing }"
                               :disabled="processing">
                Delete Record
            </jet-danger-button>
        </template>
    </jet-confirmation-modal>
    <jet-dialog-modal :show="showChangeStatusModal" @close="showChangeStatusModal = false">
        <template #title>
            Change Status
        </template>
        <template #content>
            <div class="grid grid-cols-1 gap-2 mt-4">
                <div>
                    <jet-label for="status" value="Status"/>
                    <select
                        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
                        name="status" v-model="form.status" id="status" required>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="deceased">Deceased</option>
                        <option value="closed">Closed</option>
                    </select>
                    <jet-input-error :message="form.errors.status" class="mt-2"/>
                </div>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button @click.native="showChangeStatusModal = false">
                Cancel
            </jet-secondary-button>

            <jet-success-button class="ml-2" @click.native="changeStatus"
                                  :class="{ 'opacity-25': processing }"
                                  :disabled="processing">
                Save
            </jet-success-button>
        </template>
    </jet-dialog-modal>
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
            form: this.$inertia.form({
                status: this.member.status,
                date: moment().format("YYYY-MM-DD"),

            }),
        }
    },
    methods: {
        deleteAction() {
            this.confirmingMemberDeletion = true
        },
        destroy() {

            this.$inertia.delete(this.route('members.destroy', this.member.id))
            this.confirmingMemberDeletion = false
        },
        changeStatus() {

            this.form.put(this.route('members.change_status', this.member.id),
                {
                    preserveState: false
                })
            this.showChangeStatusModal = false
        },
    },
}
</script>

<style scoped>

</style>
