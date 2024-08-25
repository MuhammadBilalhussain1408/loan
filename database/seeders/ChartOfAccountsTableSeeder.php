<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chart_of_accounts')->insert([
            [
                'name' => 'Bank Account',
                'gl_code' => '100‐00‐0000',
                'account_type' => 'bank',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Cash',
                'gl_code' => '100‐00‐0001',
                'account_type' => 'cash',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Accounts Receivable',
                'gl_code' => '100‐00‐0010',
                'account_type' => 'accounts_receivable',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Medical Equipment',
                'gl_code' => '100‐00‐0020',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Medical Equipment ‐ Accumulated Depreciation',
                'gl_code' => '100‐00‐2720',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Building & Fixtures',
                'gl_code' => '100‐00‐0021',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Building & Fixtures ‐ Accumulated Depreciation',
                'gl_code' => '100‐00‐4421',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Equipment',
                'gl_code' => '100‐00‐0022',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Equipment ‐ Accumulated Depreciation',
                'gl_code' => '100‐00‐4522',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Office Equipment',
                'gl_code' => '100‐00‐0023',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Office Equipment ‐ Accumulated Depreciation',
                'gl_code' => '100‐00‐6123',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Land',
                'gl_code' => '100‐00‐0024',
                'account_type' => 'fixed_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Petty Cash',
                'gl_code' => '100‐00‐0030',
                'account_type' => 'cash',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Refundable Deposits',
                'gl_code' => '100‐00‐0040',
                'account_type' => 'other_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Accounts Payable',
                'gl_code' => '200‐00‐0000',
                'account_type' => 'accounts_payable',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Tax Payable',
                'gl_code' => '200‐00‐0010',
                'account_type' => 'other_current_liability',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Income Taxes Payable',
                'gl_code' => '200‐00‐0020',
                'account_type' => 'income_tax',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Loans Payable',
                'gl_code' => '200‐00‐0030',
                'account_type' => 'other_current_liability',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Payroll Liabilities',
                'gl_code' => '200‐00‐0040',
                'account_type' => 'other_current_liability',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Capital Stock',
                'gl_code' => '300‐00‐0010',
                'account_type' => 'equity',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Treasury Stock',
                'gl_code' => '300‐00‐0020',
                'account_type' => 'equity',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Retained Earnings',
                'gl_code' => '300‐00‐0030',
                'account_type' => 'equity',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Partner Distributions',
                'gl_code' => '300‐00‐0030',
                'account_type' => 'equity',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Fees',
                'gl_code' => '400‐00‐0010',
                'account_type' => 'income',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Fees: Patient Fees',
                'gl_code' => '400‐00‐0011',
                'account_type' => 'income',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Fees: Consultation Fees',
                'gl_code' => '400‐00‐0012',
                'account_type' => 'income',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Refunds',
                'gl_code' => '400‐00‐0020',
                'account_type' => 'income',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Physician Salaries',
                'gl_code' => '500‐00‐0100',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Physician Assistant Salaries',
                'gl_code' => '500‐00‐0200',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Nurse Practitioner Salaries',
                'gl_code' => '500‐00‐0300',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Visiting RN Compensation',
                'gl_code' => '500‐00‐0500',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ], [
                'name' => 'Visiting LPN Compensation',
                'gl_code' => '500‐00‐0600',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Clinical Psychologist Salaries',
                'gl_code' => '500‐00‐0700',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Social Worker Salaries',
                'gl_code' => '500‐00‐0800',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Laboratory Technician Salaries',
                'gl_code' => '500‐00‐0900',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Other Nurse Salaries',
                'gl_code' => '500‐00‐1000',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ], [
                'name' => 'Transcription Salaries',
                'gl_code' => '500‐00‐1001',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Contract Labor',
                'gl_code' => '500‐00‐1002',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Agreed Upon Physician Services',
                'gl_code' => '500‐00‐1500',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Agreed Upon Physician Supervision',
                'gl_code' => '500‐00‐1600',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Medical Supplies',
                'gl_code' => '500‐00‐2500',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Transportation',
                'gl_code' => '500‐00‐2600',
                'account_type' => 'cost_of_goods_sold',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Depreciation of Medical Equipment',
                'gl_code' => '500‐00‐2700',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Professional Liability Insurance',
                'gl_code' => '500‐00‐2800',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'CME, Dues, Licenses, and Subscriptions',
                'gl_code' => '500‐00‐3200',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Electronic Health Records',
                'gl_code' => '500‐00‐3201',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Small Equipment',
                'gl_code' => '500‐00‐3202',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Rent',
                'gl_code' => '500‐00‐4000',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Insurance',
                'gl_code' => '500‐00‐4100',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Interest',
                'gl_code' => '500‐00‐4200',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ], [
                'name' => 'Utilities',
                'gl_code' => '500‐00‐4300',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Depreciation of Building and Fixtures',
                'gl_code' => '500‐00‐4400',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Depreciation of Equipment',
                'gl_code' => '500‐00‐4500',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Housekeeping and Maintenance',
                'gl_code' => '500‐00‐4600',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Property Tax',
                'gl_code' => '500‐00‐4700',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Office Salaries',
                'gl_code' => '500‐00‐6000',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Depreciation of Office Equipment',
                'gl_code' => '500‐00‐6100',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Office Supplies',
                'gl_code' => '500‐00‐6200',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Legal',
                'gl_code' => '500‐00‐6300',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Accounting',
                'gl_code' => '500‐00‐6400',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Administrative Insurance',
                'gl_code' => '500‐00‐6500',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Telephone',
                'gl_code' => '500‐00‐6600',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Fringe Benefits & Payroll Taxes',
                'gl_code' => '500‐00‐6700',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Billing Service',
                'gl_code' => '500‐00‐6800',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Miscellaneous',
                'gl_code' => '500‐00‐6800',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Non‐Allowable Costs',
                'gl_code' => '500‐00‐6802',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Corporate Administrative Allocation',
                'gl_code' => '500‐00‐6803',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Pharmacy',
                'gl_code' => '500‐00‐7500',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Dental',
                'gl_code' => '500‐00‐7600',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Optometry',
                'gl_code' => '500‐00‐7700',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Telehealth',
                'gl_code' => '500‐00‐7900',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Chronic Care Management',
                'gl_code' => '500‐00‐8000',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'EPSDT/Physicals',
                'gl_code' => '500‐00‐8100',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Hospital',
                'gl_code' => '500‐00‐8101',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Private Practice',
                'gl_code' => '500‐00‐8102',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Laboratory',
                'gl_code' => '500‐00‐8103',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Radiology',
                'gl_code' => '500‐00‐8104',
                'account_type' => 'expense',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            //inventory accounts
            [
                'name' => 'Inventory',
                'gl_code' => '500‐00‐8104',
                'account_type' => 'current_asset',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
            [
                'name' => 'Sales Tax Payable',
                'gl_code' => '200‐00‐0010',
                'account_type' => 'other_current_liability',
                'enable_reconciliation' => 1,
                'allow_manual' => 1,
                'description' => '',
            ],
        ]);
    }
}
