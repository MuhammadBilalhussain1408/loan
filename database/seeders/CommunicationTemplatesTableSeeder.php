<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunicationTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('communication_templates')->insert([
            [
                'name' => 'Loan Application Approval Stage Status Changed Member Notification Email',
                'system_name' => 'loan_application_approval_stage_status_changed_member_notification_email',
                'type' => 'email',
                'subject' => 'Loan Application Approval Stage Status Changed',
                'description' => 'Hello {{memberFirstName}}, your loan application #{{loanApplicationID}} status for stage {{loanApplicationStageName}} has changed to {{loanApplicationStageStatus}}.',
                'is_system' => 1,

            ],
            [
                'name' => 'Loan Application Approval Stage Status Changed Member Notification SMS',
                'system_name' => 'loan_application_approval_stage_status_changed_member_notification_sms',
                'type' => 'sms',
                'subject' => 'Loan Application Approval Stage Status Changed',
                'description' => 'Hello {{memberFirstName}}, your loan application #{{loanApplicationID}} status for stage {{loanApplicationStageName}} has changed to {{loanApplicationStageStatus}}.',
                'is_system' => 1,

            ],
            [
                'name' => 'Loan Application Status Changed Member Notification Email',
                'system_name' => 'loan_application_status_changed_member_notification_email',
                'type' => 'email',
                'subject' => 'Loan Application Status Changed',
                'description' => 'Hello {{memberFirstName}}, your loan application #{{loanApplicationID}} status has changed to {{loanApplicationStatus}}.',
                'is_system' => 1,

            ],
            [
                'name' => 'Loan Application Status Changed Member Notification SMS',
                'system_name' => 'loan_application_status_changed_member_notification_sms',
                'type' => 'sms',
                'subject' => 'Loan Application Status Changed',
                'description' => 'Hello {{memberFirstName}}, your loan application #{{loanApplicationID}} status has changed to {{loanApplicationStatus}}.',
                'is_system' => 1,

            ],
            [
                'name' => 'Loan Status Changed Member Notification Email',
                'system_name' => 'loan_status_changed_member_notification_email',
                'type' => 'email',
                'subject' => 'Loan Status Changed',
                'description' => 'Hello {{memberFirstName}}, your loan #{{loanID}} status has changed to {{loanStatus}}.',
                'is_system' => 1,

            ],
            [
                'name' => 'Loan Status Changed Member Notification SMS',
                'system_name' => 'loan_status_changed_member_notification_sms',
                'type' => 'sms',
                'subject' => 'Loan Status Changed',
                'description' => 'Hello {{memberFirstName}}, your loan #{{loanID}} status has changed to {{loanStatus}}.',
                'is_system' => 1,

            ],
        ]);
    }
}
