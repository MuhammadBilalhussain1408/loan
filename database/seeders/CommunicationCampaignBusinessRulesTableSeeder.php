<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunicationCampaignBusinessRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('communication_campaign_business_rules')->insert([
            [
                'name' => 'Single Member',
                'is_trigger' => 0,
                'description' => 'Sends to single member',
                'active' => 1,

            ],
            [
                'name' => 'Single User',
                'is_trigger' => 0,
                'description' => 'Sends to single user',
                'active' => 1,

            ],
            [
                'name' => 'Active Members',
                'is_trigger' => 0,
                'description' => 'All members with the status ‘Active’',
                'active' => 1,

            ],
            [
                'name' => 'Prospective Members',
                'is_trigger' => 0,
                'description' => 'All members with the status ‘Active’ who have never had a loan before',
                'active' => 1,
            ],
            [
                'name' => 'Active Loan Members',
                'is_trigger' => 0,
                'description' => 'All members with an outstanding loan',
                'active' => 1,
            ],
            [
                'name' => 'Loans in arrears',
                'is_trigger' => 0,
                'description' => 'All members with an outstanding loan in arrears between X and Y days',
                'active' => 1,

            ],
            [
                'name' => 'Loans disbursed to members',
                'is_trigger' => 0,
                'description' => 'All members who have had a loan disbursed to them in the last X to Y days',
                'active' => 1,
            ],
            [
                'name' => 'Loan payments due',
                'is_trigger' => 0,
                'description' => 'All members with an unpaid installment due on their loan between X and Y days',
                'active' => 1,
            ], [
                'name' => 'Upcoming payments reminder',
                'is_trigger' => 0,
                'description' => 'All members with an upcoming installment due on their loan in X days',
                'active' => 1,
            ],
            [
                'name' => 'Loan Repayment',
                'is_trigger' => 1,
                'description' => 'Loan Repayment',
                'active' => 1,
            ],
            [
                'name' => 'Loan Disbursed',
                'is_trigger' => 1,
                'description' => 'Loan Disbursed',
                'active' => 1,
            ],
            [
                'name' => 'Loan Rescheduled',
                'is_trigger' => 1,
                'description' => 'Loan Rescheduled',
                'active' => 1,
            ],
            [
                'name' => 'Loan Closed',
                'is_trigger' => 1,
                'description' => 'Loan Closed',
                'active' => 1,
            ],
            [
                'name' => 'Dormant Prospects',
                'is_trigger' => 0,
                'description' => 'All individuals who have not yet received a loan but were also entered into the system more than 3 months',
                'active' => 0,

            ],
            [
                'name' => 'Loan Payments Due (Overdue Loans)',
                'is_trigger' => 0,
                'description' => 'Loan Payments Due between X to Y days for members in arrears between X and Y days',
                'active' => 0,

            ],
            [
                'name' => 'Loan Payments Received (Active Loans)',
                'is_trigger' => 0,
                'description' => 'Payments received in the last X to Y days for any loan with the status Active (on-time)',
                'active' => 0,

            ],
            [
                'name' => 'Loan Payments Received (Overdue Loans)',
                'is_trigger' => 0,
                'description' => 'Payments received in the last X to Y days for any loan with the status Overdue (arrears) between X and Y days',
                'active' => 0,

            ],
            [
                'name' => 'Happy Birthday',
                'is_trigger' => 0,
                'description' => 'This sends a message to all members with the status Active on their Birthday',
                'active' => 0,
            ],
            [
                'name' => 'Loan Fully Repaid',
                'is_trigger' => 0,
                'description' => 'All loans that have been fully repaid (Closed or Overpaid) in the last X to Y days',
                'active' => 0,
            ],

            [
                'name' => 'Loans Outstanding after final instalment date',
                'is_trigger' => 0,
                'description' => 'All active loans (with an outstanding balance) between X to Y days after the final instalment date on their loan schedule',
                'active' => 0,
            ],
            [
                'name' => 'Past Loan Members',
                'is_trigger' => 0,
                'description' => 'Past Loan Members who have previously had a loan but do not currently have one and finished repaying their most recent loan in the last X to Y days.',
                'active' => 0,
            ],
        ]);

    }
}
