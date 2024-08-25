<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call([
            CountriesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            SettingsTableSeeder::class,
            UsersTableSeeder::class,
            CurrenciesTableSeeder::class,
            TimezonesTableSeeder::class,
            PaymentTypesTableSeeder::class,
            CommunicationCampaignAttachmentTypesTableSeederTableSeeder::class,
            CommunicationCampaignBusinessRulesTableSeeder::class,
            CommunicationBusinessRulesTableSeeder::class,
            CommunicationTemplatesTableSeeder::class,
            BranchesTableSeeder::class,
            SmsGatewaysTableSeeder::class,
            LoanChargeOptionsTableSeeder::class,
            LoanChargeTypesTableSeeder::class,
            LoanCreditChecksTableSeeder::class,
            LoanTransactionProcessingStrategiesTableSeeder::class,
            LoanTransactionTypesTableSeeder::class,
            LoanProvisioningTableSeeder::class,
            MemberRelationshipTableSeeder::class,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
