<?php


namespace App\Actions\Reports;


use App\Models\Branch;
use App\Models\Currency;
use App\Models\FinancialPeriod;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\SavingsTransaction;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class Reports
{
    public function getLoansByStatus($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $paidBy = $data['paid_by'] ?? '';
        $coPayerID = $data['co_payer_id'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            if ($currency->xrate > $baseCurrency->xrate) {
                $operator = '/';
            } else {
                $operator = '*';
            }
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        return Loan::when(($startDate && $endDate), function (Builder $query) use ($startDate, $endDate) {
                $query->whereBetween('submitted_on_date', [$startDate, $endDate]);
            })
            ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                $query->where('currency_id', $currencyID);
            })

            ->when($branchID, function ($query) use ($branchID) {
                $query->where('branch_id', $branchID);
            })
            ->selectRaw('count(id) as total_count,sum(coalesce(if(xrate>1,principal*xrate,principal/xrate),0)) as principal,status')
            ->groupBy('status')
            ->get();
    }
    public function getIncomeExpenses($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $paidBy = $data['paid_by'] ?? '';
        $coPayerID = $data['co_payer_id'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            if ($currency->xrate > $baseCurrency->xrate) {
                $operator = '/';
            } else {
                $operator = '*';
            }
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $expenses = JournalEntry::leftJoin('chart_of_accounts', 'chart_of_accounts.id', 'journal_entries.chart_of_account_id')
            ->where('chart_of_accounts.account_type', 'expense')
            ->when(($startDate && $endDate), function (Builder $query) use ($startDate, $endDate) {
                $query->whereBetween('journal_entries.date', [$startDate, $endDate]);
            })
            ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                $query->where('journal_entries.currency_id', $currencyID);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('journal_entries.branch_id', $branchID);
            })
            ->selectRaw('coalesce(sum(if(currency_id=' . $currencyID . ',(coalesce(debit,0)-coalesce(credit,0)),(coalesce(debit,0)-coalesce(credit,0))' . $operator . 'xrate)),0) as amount')
            ->first()->amount;
        $income = JournalEntry::leftJoin('chart_of_accounts', 'chart_of_accounts.id', 'journal_entries.chart_of_account_id')
            ->where('chart_of_accounts.account_type', 'income')
            ->when(($startDate && $endDate), function (Builder $query) use ($startDate, $endDate) {
                $query->whereBetween('journal_entries.date', [$startDate, $endDate]);
            })
            ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                $query->where('journal_entries.currency_id', $currencyID);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('journal_entries.branch_id', $branchID);
            })
            ->selectRaw('coalesce(sum(if(currency_id=' . $currencyID . ',(coalesce(credit,0)-coalesce(debit,0)),(coalesce(credit,0)-coalesce(debit,0))' . $operator . 'xrate)),0) as amount')
            ->first()->amount;
        return [
            'expenses' => $expenses,
            'income' => $income,
        ];
    }

    public function getPeriodIncomeExpenses($data = [])
    {
        $period = $data['period'] ?? 'week';
        $startDate = !empty($data['start_date']) ? Carbon::parse($data['start_date']) : Carbon::today();
        $endDate = $data['end_date'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $paidBy = $data['paid_by'] ?? '';
        $coPayerID = $data['co_payer_id'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paymentTypeID = $data['payment_type_id'] ?? '';
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            if ($currency->xrate > $baseCurrency->xrate) {
                $operator = '/';
            } else {
                $operator = '*';
            }
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $chartData = [];
        $limit = 7;
        $add = 'day';
        if ($period === 'week') {
            $startDate = $startDate->startOf('week');
            $limit = 7;
            $add = 'day';
            $label = $startDate->format('D');
        }
        if ($period === 'month') {
            $startDate = $startDate->startOf('month');
            $limit = 31;
            $add = 'day';
            $label = $startDate->format('Y-m-d');
        }
        if ($period === 'year') {
            $startDate = $startDate->startOf('year');
            $limit = 12;
            $add = 'month';
            $label = $startDate->format('Y-m-d');
        }
        for ($i = 0; $i < $limit; $i++) {
            if ($period === 'week') {
                $label = $startDate->format('D');
            }
            if ($period === 'month') {
                $label = $startDate->format('Y-m-d');
            }
            if ($period === 'year') {
                $label = $startDate->format('M Y');
            }
            $expenses = JournalEntry::leftJoin('chart_of_accounts', 'chart_of_accounts.id', 'journal_entries.chart_of_account_id')
                ->where('chart_of_accounts.account_type', 'expense')
                ->when($period, function ($query) use ($period, $startDate) {
                    if ($period === 'week') {
                        $query->where('journal_entries.date', $startDate->format('Y-m-d'));
                    }
                    if ($period === 'month') {
                        $query->where('journal_entries.date', $startDate->format('Y-m-d'));
                    }
                    if ($period === 'year') {
                        $query->whereBetween('journal_entries.date', [$startDate->startOfMonth()->format('Y-m-d'), $startDate->endOfMonth()->format('Y-m-d')]);
                    }
                })
                ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                    $query->where('journal_entries.currency_id', $currencyID);
                })
                ->when($branchID, function ($query) use ($branchID) {
                    $query->where('journal_entries.branch_id', $branchID);
                })
                ->selectRaw('coalesce(sum(if(currency_id=' . $currencyID . ',(coalesce(debit,0)-coalesce(credit,0)),(coalesce(debit,0)-coalesce(credit,0))' . $operator . 'xrate)),0) as amount')
                ->first()->amount;
            $income = JournalEntry::leftJoin('chart_of_accounts', 'chart_of_accounts.id', 'journal_entries.chart_of_account_id')
                ->where('chart_of_accounts.account_type', 'income')
                ->when($period, function ($query) use ($period, $startDate) {
                    if ($period === 'week') {
                        $query->where('journal_entries.date', $startDate->format('Y-m-d'));
                    }
                    if ($period === 'month') {
                        $query->where('journal_entries.date', $startDate->format('Y-m-d'));
                    }
                    if ($period === 'year') {
                        $query->whereBetween('journal_entries.date', [$startDate->startOfMonth()->format('Y-m-d'), $startDate->endOfMonth()->format('Y-m-d')]);
                    }
                })
                ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                    $query->where('journal_entries.currency_id', $currencyID);
                })
                ->when($branchID, function ($query) use ($branchID) {
                    $query->where('journal_entries.branch_id', $branchID);
                })
                ->selectRaw('coalesce(sum(if(currency_id=' . $currencyID . ',(coalesce(credit,0)-coalesce(debit,0)),if(xrate<1,coalesce((coalesce(credit,0)-coalesce(debit,0))*xrate,0),coalesce((coalesce(credit,0)-coalesce(debit,0))/xrate,0)))),0) as amount')
                ->first()->amount;
            $chartData[] = [
                'expenses' => $expenses,
                'income' => $income,
                'label' => $label
            ];
            $startDate = $startDate->add($add, 1, false);
        }
        return $chartData;
    }


    public function getStaffReport($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $userID = $data['user_id'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $status = $data['status'] ?? '';
        $createdByID = $data['created_by_id'] ?? '';
        $active = $data['active'] ?? '';

        $search = $data['search'] ?? '';
        $orderBy = $data['order_by'] ?? 'total_clients';
        $paginate = $data['paginate'] ?? false;
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            if ($currency->xrate > $baseCurrency->xrate) {
                $operator = '/';
            } else {
                $operator = '*';
            }
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $query = User::selectRaw("users.*")
            ->selectSub(function ($query) use ($branchID, $startDate, $endDate, $status, $currencyID, $currencyFromBase) {
                $query->from('loans')
                    ->whereColumn('loans.loan_officer_id', 'users.id')
                    ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('loans.submitted_on_date', [$startDate, $endDate]);
                    })
                    ->when($branchID, function ($query) use ($branchID) {
                        $query->where('loans.branch_id', $branchID);
                    })
                    ->selectRaw('count(id)')
                    ->limit(1);

            }, 'total_loans')
            ->selectSub(function ($query) use ($branchID, $startDate, $endDate, $status, $currencyID, $currencyFromBase) {
                $query->from('clients')
                    ->whereColumn('clients.loan_officer_id', 'users.id')
                    ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('clients.created_date', [$startDate, $endDate]);
                    })
                    ->when($branchID, function ($query) use ($branchID) {
                        $query->where('clients.branch_id', $branchID);
                    })
                    ->selectRaw('count(id)')
                    ->limit(1);
            }, 'total_clients')
            ->selectSub(function ($query) use ($branchID, $startDate, $endDate, $status, $currencyID, $currencyFromBase) {
                $query->from('savings')
                    ->whereColumn('savings.savings_officer_id', 'users.id')
                    ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('savings.submitted_on_date', [$startDate, $endDate]);
                    })
                    ->when($branchID, function ($query) use ($branchID) {
                        $query->where('savings.branch_id', $branchID);
                    })
                    ->selectRaw('count(id)')
                    ->limit(1);
            }, 'total_savings')
            ->selectSub(function ($query) use ($branchID, $startDate, $endDate, $currencyID, $currencyFromBase, $status) {
                $query->selectRaw('coalesce(sum(if(loans.xrate>1,coalesce(loans.principal/loans.xrate,0),coalesce(loans.principal*loans.xrate,0))),0)')
                    ->from('loans')
                    ->whereColumn('loans.loan_officer_id', 'users.id')
                    ->when($branchID, function ($query) use ($branchID) {
                        $query->where('loans.branch', $branchID);
                    })
                    ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                        $query->where('loans.currency_id', $currencyID);
                    })
                    ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('loans.submitted_on_date', [$startDate, $endDate]);
                    })
                    ->when($status, function ($query) use ($status) {
                        $query->where('loans.status', $status);
                    })
                    ->limit(1);
            }, 'total_principal')
            ->selectSub(function ($query) use ($branchID, $startDate, $endDate, $currencyID, $currencyFromBase, $status) {
                $query->selectRaw('coalesce(sum(if(loan_transactions.xrate>1,coalesce(loan_transactions.amount/loan_transactions.xrate,0),coalesce(loan_transactions.amount*loan_transactions.xrate,0))),0)')
                    ->from('loan_transactions')
                    ->join('loans', 'loans.id', 'loan_transactions.loan_id')
                    ->whereColumn('loans.loan_officer_id', 'users.id')
                    ->where('loan_transaction_type_id', LoanTransactionType::where('name', 'Repayment')->first()->id)
                    ->when($branchID, function ($query) use ($branchID) {
                        $query->where('loans.branch', $branchID);
                    })
                    ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                        $query->where('loan_transactions.currency_id', $currencyID);
                    })
                    ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('loan_transactions.submitted_on', [$startDate, $endDate]);
                    })
                    ->when($status, function ($query) use ($status) {
                        $query->where('loans.status', $status);
                    })
                    ->limit(1);
            }, 'total_payments')
            ->havingRaw('total_clients>0')
            ->when($search, function ($query) use ($search) {
                $query->where('users.first_name', 'like', '%' . $search . '%');
                $query->orWhere('users.last_name', 'like', '%' . $search . '%');
                $query->orWhere('users.id', 'like', '%' . $search . '%');
                $query->orWhere('users.description', 'like', '%' . $search . '%');
            })
            ->when($userID, function ($query) use ($userID) {
                $query->where('users.id', $userID);
            })
            ->groupBy('users.id')
            ->orderBy($orderBy, 'desc');
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    public function getTopStaffReport($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $inventoryWarehouseID = $data['inventory_warehouse_id'] ?? '';
        $inventoryCategoryID = $data['inventory_category_id'] ?? '';
        $inventorySubCategoryID = $data['inventory_sub_category_id'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $status = $data['status'] ?? '';
        $paymentStatus = $data['payment_status'] ?? '';
        $brandID = $data['inventory_product_brand_id'] ?? '';
        $patientID = $data['patient_id'] ?? '';
        $createdByID = $data['created_by_id'] ?? '';
        $active = $data['active'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $search = $data['search'] ?? '';
        $orderBy = $data['order_by'] ?? 'total_sales_quantity';
        $paginate = $data['paginate'] ?? false;
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            if ($currency->xrate > $baseCurrency->xrate) {
                $operator = '/';
            } else {
                $operator = '*';
            }
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $query = User::leftJoin('inventory_product_sales', 'inventory_product_sales.created_by_id', 'users.id')
            ->selectRaw("users.*")
            ->selectSub(function ($query) use ($inventoryWarehouseID, $startDate, $endDate, $inventoryCategoryID, $inventorySubCategoryID, $brandID, $status, $paymentStatus, $currencyID, $currencyFromBase) {
                $query->selectRaw('coalesce(sum(quantity),0)')
                    ->from('inventory_product_sale_items')
                    ->leftJoin('inventory_product_sales', 'inventory_product_sales.id', 'inventory_product_sale_items.inventory_product_sale_id')
                    ->whereColumn('inventory_product_sales.created_by_id', 'users.id')
                    ->when($inventoryWarehouseID, function ($query) use ($inventoryWarehouseID) {
                        $query->where('inventory_product_sales.inventory_warehouse_id', $inventoryWarehouseID);
                    })
                    ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('inventory_product_sales.sale_date', [$startDate, $endDate]);
                    })
                    ->when($inventoryCategoryID, function ($query) use ($inventoryCategoryID) {
                        $query->where('inventory_products.inventory_product_category_id', $inventoryCategoryID);
                    })
                    ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                        $query->where('inventory_product_sales.currency_id', $currencyID);
                    })
                    ->when($status, function ($query) use ($status) {
                        $query->where('inventory_product_sales.status', $status);
                    })
                    ->when($paymentStatus, function ($query) use ($paymentStatus) {
                        $query->whereHas('invoice', function ($query) use ($paymentStatus) {
                            $query->where('status', $paymentStatus);
                        });
                    })
                    ->when($inventorySubCategoryID, function ($query) use ($inventorySubCategoryID) {
                        $query->where('inventory_products.inventory_product_subcategory_id', $inventorySubCategoryID);
                    })
                    ->when($brandID, function ($query) use ($brandID) {
                        $query->where('inventory_products.inventory_product_brand_id', $brandID);
                    })
                    ->limit(1);
            }, 'total_sales_quantity')
            ->selectSub(function ($query) use ($inventoryWarehouseID, $startDate, $endDate, $currencyID, $currencyFromBase, $status, $paymentStatus) {
                $query->selectRaw('coalesce(sum(if(inventory_product_sale_items.xrate>1,coalesce(inventory_product_sale_items.total/inventory_product_sale_items.xrate,0),coalesce(inventory_product_sale_items.total*inventory_product_sale_items.xrate,0))),0)')
                    ->from('inventory_product_sale_items')
                    ->leftJoin('inventory_product_sales', 'inventory_product_sales.id', 'inventory_product_sale_items.inventory_product_sale_id')
                    ->whereColumn('inventory_product_sales.created_by_id', 'users.id')
                    ->when($inventoryWarehouseID, function ($query) use ($inventoryWarehouseID) {
                        $query->where('inventory_product_sales.inventory_warehouse_id', $inventoryWarehouseID);
                    })
                    ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                        $query->where('inventory_product_sales.currency_id', $currencyID);
                    })
                    ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('inventory_product_sales.sale_date', [$startDate, $endDate]);
                    })
                    ->when($status, function ($query) use ($status) {
                        $query->where('inventory_product_sales.status', $status);
                    })
                    ->when($paymentStatus, function ($query) use ($paymentStatus) {
                        $query->whereHas('invoice', function ($query) use ($paymentStatus) {
                            $query->where('status', $paymentStatus);
                        });
                    })
                    ->limit(1);
            }, 'total_sales_amount')
            ->selectSub(function ($query) use ($inventoryWarehouseID, $startDate, $endDate, $currencyID, $currencyFromBase, $status, $paymentStatus) {
                $query->selectRaw('coalesce(sum(if(inventory_product_sales.xrate>1,coalesce(inventory_product_sales.balance/inventory_product_sales.xrate,0),coalesce(inventory_product_sales.balance*inventory_product_sales.xrate,0))),0)')
                    ->from('inventory_product_sales')
                    ->whereColumn('inventory_product_sales.created_by_id', 'users.id')
                    ->when($inventoryWarehouseID, function ($query) use ($inventoryWarehouseID) {
                        $query->where('inventory_product_sales.inventory_warehouse_id', $inventoryWarehouseID);
                    })
                    ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                        $query->where('inventory_product_sales.currency_id', $currencyID);
                    })
                    ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('inventory_product_sales.sale_date', [$startDate, $endDate]);
                    })
                    ->when($status, function ($query) use ($status) {
                        $query->where('inventory_product_sales.status', $status);
                    })
                    ->when($paymentStatus, function ($query) use ($paymentStatus) {
                        $query->whereHas('invoice', function ($query) use ($paymentStatus) {
                            $query->where('status', $paymentStatus);
                        });
                    })
                    ->limit(1);
            }, 'total_balance')
            ->whereHas('sales')
            ->when($search, function ($query) use ($search) {
                $query->where('users.first_name', 'like', '%' . $search . '%');
                $query->orWhere('users.last_name', 'like', '%' . $search . '%');
                $query->orWhere('users.id', 'like', '%' . $search . '%');
                $query->orWhere('users.description', 'like', '%' . $search . '%');
            })
            ->when($createdByID, function ($query) use ($createdByID) {
                $query->where('users.id', $createdByID);
            })
            ->groupBy('users.id')
            ->orderBy($orderBy, 'desc')
            ->limit(10);

        return $query->get();
    }


    public function getTrialBalance($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $createdByID = $data['created_by_id'] ?? '';
        $active = $data['active'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $search = $data['search'] ?? '';
        $orderBy = $data['order_by'] ?? 'created_at';
        $paginate = $data['paginate'] ?? false;
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $financialPeriod = FinancialPeriod::where('closed', 0)->first();
        $query = JournalEntry::leftJoin('chart_of_accounts', 'journal_entries.chart_of_account_id', 'chart_of_accounts.id')
            ->selectRaw("coalesce(sum(if(journal_entries.xrate>1,coalesce(journal_entries.debit/journal_entries.xrate,0),coalesce(journal_entries.debit*journal_entries.xrate,0))),0) debit,coalesce(sum(if(journal_entries.xrate>1,coalesce(journal_entries.credit/journal_entries.xrate,0),coalesce(journal_entries.credit*journal_entries.xrate,0))),0) credit,chart_of_accounts.name")
            ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                $query->where('journal_entries.currency_id', $currencyID);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('journal_entries.date', '<=', $endDate);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('journal_entries.branch_id', $branchID);
            })
            ->where('journal_entries.financial_period_id', $financialPeriod->id)
            ->groupBy('chart_of_accounts.id');

        return $query->get();
    }

    public function getLedgerReport($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $createdByID = $data['created_by_id'] ?? '';
        $active = $data['active'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $search = $data['search'] ?? '';
        $financialPeriodID = $data['financial_period_id'] ?? '';
        $orderBy = $data['order_by'] ?? 'created_at';
        $paginate = $data['paginate'] ?? false;
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $query = JournalEntry::leftJoin('chart_of_accounts', 'journal_entries.chart_of_account_id', 'chart_of_accounts.id')
            ->selectRaw("coalesce(sum(if(journal_entries.xrate>1,coalesce(journal_entries.debit/journal_entries.xrate,0),coalesce(journal_entries.debit*journal_entries.xrate,0))),0) debit,coalesce(sum(if(journal_entries.xrate>1,coalesce(journal_entries.credit/journal_entries.xrate,0),coalesce(journal_entries.credit*journal_entries.xrate,0))),0) credit,chart_of_accounts.name,chart_of_accounts.gl_code,chart_of_accounts.account_type")
            ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                $query->where('journal_entries.currency_id', $currencyID);
            })
            ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween('journal_entries.date', [$startDate, $endDate]);
            })
            ->when($financialPeriodID, function ($query) use ($financialPeriodID) {
                $query->where('journal_entries.financial_period_id', $financialPeriodID);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('journal_entries.branch_id', $branchID);
            })
            ->orderBy('account_type')
            ->groupBy('chart_of_accounts.id');

        return $query->get();
    }

    public function getBalanceSheet($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $createdByID = $data['created_by_id'] ?? '';
        $active = $data['active'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $search = $data['search'] ?? '';
        $orderBy = $data['order_by'] ?? 'created_at';
        $paginate = $data['paginate'] ?? false;
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $financialPeriod = !empty($data['financial_period_id']) ? FinancialPeriod::find($data['financial_period_id']) : FinancialPeriod::where('closed', 0)->first();
        $entries = JournalEntry::leftJoin('chart_of_accounts', 'journal_entries.chart_of_account_id', 'chart_of_accounts.id')
            ->selectRaw("
                        case
                            when account_type = 'equity' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0))), 0)
                            when account_type = 'fixed_asset' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                            when account_type = 'current_asset' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                            when account_type = 'other_asset' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                            when account_type = 'cash' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                            when account_type = 'bank' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                            when account_type = 'stock' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                            when account_type = 'other_current_liability' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0))), 0)
                            when account_type = 'credit_card' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0))), 0)
                            when account_type = 'long_term_liability' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0))), 0)
                            when account_type = 'other_liability' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0))), 0)
                            when account_type = 'income_tax' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0))), 0)

                        END as balance,
                        chart_of_accounts.name,
                        chart_of_accounts.gl_code,
                        chart_of_accounts.account_type
                        ")
            ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                $query->where('journal_entries.currency_id', $currencyID);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('journal_entries.date', '<=', $endDate);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('journal_entries.branch_id', $branchID);
            })
            ->whereIn('account_type', ['equity', 'fixed_asset', 'current_asset', 'current_asset', 'other_asset', 'cash', 'bank', 'stock', 'other_current_liability', 'credit_card', 'long_term_liability', 'other_liability', 'income_tax'])
            //->where('journal_entries.financial_period_id', $financialPeriod->id)
            ->where('chart_of_accounts.account_type', 'equity')
            ->groupBy('chart_of_accounts.id')->get();


        return [
            'assets' => $entries->whereIn('account_type', ['fixed_asset', 'current_asset', 'current_asset', 'other_asset', 'cash', 'bank', 'stock']),
            'liabilities' => $entries->whereIn('account_type', ['other_current_liability', 'credit_card', 'long_term_liability', 'other_liability', 'income_tax']),
            'equity' => $entries->whereIn('account_type', ['equity']),
        ];
    }

    public function getIncomeStatement($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $createdByID = $data['created_by_id'] ?? '';
        $active = $data['active'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $financialPeriodID = $data['financial_period_id'] ?? '';
        $search = $data['search'] ?? '';
        $orderBy = $data['order_by'] ?? 'created_at';
        $paginate = $data['paginate'] ?? false;
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $entries = JournalEntry::leftJoin('chart_of_accounts', 'journal_entries.chart_of_account_id', 'chart_of_accounts.id')
            ->selectRaw("
                        case
                            when account_type = 'income' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0))), 0)
                            when account_type = 'other_income' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0))), 0)
                            when account_type = 'expense' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                            when account_type = 'cost_of_goods_sold' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                            when account_type = 'other_expense' then
                                coalesce(sum(if(journal_entries.xrate > 1, coalesce(journal_entries.debit / journal_entries.xrate, 0),coalesce(journal_entries.debit * journal_entries.xrate, 0)))-sum(if(journal_entries.xrate > 1, coalesce(journal_entries.credit / journal_entries.xrate, 0),coalesce(journal_entries.credit * journal_entries.xrate, 0))), 0)
                        END as balance,
                        chart_of_accounts.name,
                        chart_of_accounts.gl_code,
                        chart_of_accounts.account_type
                        ")
            ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                $query->where('journal_entries.currency_id', $currencyID);
            })
            ->when(($startDate && $endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween('journal_entries.date', [$startDate, $endDate]);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('journal_entries.branch_id', $branchID);
            })
            ->when($financialPeriodID, function ($query) use ($financialPeriodID) {
                $query->where('journal_entries.financial_period_id', $financialPeriodID);
            })
            ->whereIn('account_type', ['income', 'other_income', 'expense', 'cost_of_goods_sold', 'other_expense'])
            ->where('chart_of_accounts.account_type', 'equity')
            ->groupBy('chart_of_accounts.id')->get();


        return [
            'expenses' => $entries->whereIn('account_type', ['cost_of_goods_sold']),
            'other_expenses' => $entries->whereIn('account_type', ['expense', 'other_expense']),
            'income' => $entries->whereIn('account_type', ['income']),
            'other_income' => $entries->whereIn('account_type', ['other_income']),
        ];
    }

    public function getSystemFinancialSummaryReport($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $startTime = $data['start_time'] ?? '';
        $endTime = $data['end_time'] ?? '';
        $currencyID = $data['currency_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $referringPractitionerID = $data['referring_practitioner_id'] ?? '';
        $createdByType = $data['created_by_type'] ?? '';
        $patientID = $data['patient_id'] ?? '';
        $doctorID = $data['doctor_id'] ?? '';
        $coPayerID = $data['co_payer_id'] ?? '';
        $status = $data['status'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $baseCurrency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value);
        $operator = '/';
        if ($currencyID) {
            $currency = Currency::find($currencyID);
            if ($currency->xrate > $baseCurrency->xrate) {
                $operator = '/';
            } else {
                $operator = '*';
            }
            $currencyFromBase = false;
        } else {
            $currencyID = $baseCurrency->id;
            $currencyFromBase = true;
        }
        $query = User::selectSub(function ($query) use ($doctorID, $patientID, $currencyID, $currencyFromBase, $branchID, $coPayerID, $startDate, $endDate) {
            $query->selectRaw('coalesce(sum(if(currency_id=' . $currencyID . ',coalesce(amount,0),if(xrate<1,coalesce(amount*xrate,0),coalesce(amount/xrate,0)))),0)')
                ->from('invoices')
                ->whereColumn('invoices.doctor_id', 'users.id')
                ->when($patientID, function ($query) use ($patientID) {
                    $query->where('invoices.patient_id', $patientID);
                })
                ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                    $query->where('invoices.currency_id', $currencyID);
                })
                ->when($branchID, function ($query) use ($branchID) {
                    $query->where('invoices.branch_id', $branchID);
                })
                ->when($coPayerID, function ($query) use ($coPayerID) {
                    $query->where('invoices.co_payer_id', $coPayerID);
                })
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('invoices.date', [$startDate, $endDate]);
                })
                ->groupBy('doctor_id')
                ->limit(1);
        }, 'amount')
            ->selectSub(function ($query) use ($doctorID, $patientID, $currencyID, $currencyFromBase, $branchID, $coPayerID, $startDate, $endDate) {
                $query->selectRaw('coalesce(sum(if(invoice_payments.currency_id=' . $currencyID . ',coalesce(invoice_payments.amount,0),if(invoice_payments.xrate<1,coalesce(invoice_payments.amount*invoice_payments.xrate,0),coalesce(invoice_payments.amount/invoice_payments.xrate,0)))),0)')
                    ->from('invoice_payments')
                    ->join('invoices', 'invoices.id', 'invoice_payments.invoice_id')
                    ->whereColumn('invoices.doctor_id', 'users.id')
                    ->when($patientID, function ($query) use ($patientID) {
                        $query->where('invoices.patient_id', $patientID);
                    })
                    ->when($currencyID && !$currencyFromBase, function ($query) use ($currencyID) {
                        $query->where('invoices.currency_id', $currencyID);
                    })
                    ->when($branchID, function ($query) use ($branchID) {
                        $query->where('invoices.branch_id', $branchID);
                    })
                    ->when($coPayerID, function ($query) use ($coPayerID) {
                        $query->where('invoices.co_payer_id', $coPayerID);
                    })
                    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('invoices.date', [$startDate, $endDate]);
                    })
                    ->groupBy('invoices.doctor_id')
                    ->limit(1);
            }, 'payments')
            ->when($doctorID, function ($query) use ($doctorID) {
                $query->where('users.id', $doctorID);
            })
            ->selectRaw('users.*')
            ->havingRaw('amount>0 or payments>0');

        if (!empty($paginate)) {
            $results = $query->paginate();

        } else {

            $results = $query->get();
        }
        return $results;
    }


    public function getAuditTrailReport($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $roleID = $data['role_id'] ?? '';
        $doctorID = $data['doctor_id'] ?? '';
        $userID = $data['user_id'] ?? '';
        $status = $data['status'] ?? '';
        $gender = $data['gender'] ?? '';
        $search = $data['search'] ?? '';
        $orderBy = $data['order_by'] ?? 'created_at';
        $paginate = $data['paginate'] ?? false;
        $query = Activity::with(['causer'])
            ->when(($startDate && $endDate), function (Builder $query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($userID, function (Builder $query) use ($userID) {
                $query->where('causer_id', $userID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('users.first_name', 'like', '%' . $search . '%');
                $query->orWhere('users.id', 'like', '%' . $search . '%');
                $query->orWhere('users.practice_number', 'like', '%' . $search . '%');
                $query->orWhere('users.description', 'like', '%' . $search . '%');
            })
            ->when($roleID, function ($query) use ($roleID) {
                $query->whereHas('roles', function ($query) use ($roleID) {
                    $query->where('id', $roleID);
                });
            })
            ->orderBy($orderBy, 'desc');
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    public function getActiveUsersReport($data = []): Collection|array|LengthAwarePaginator
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $roleID = $data['role_id'] ?? '';
        $doctorID = $data['doctor_id'] ?? '';
        $coPayerID = $data['co_payer_id'] ?? '';
        $status = $data['status'] ?? '';
        $gender = $data['gender'] ?? '';
        $search = $data['search'] ?? '';
        $orderBy = $data['order_by'] ?? 'last_login';
        $paginate = $data['paginate'] ?? false;
        $query = User::with(['roles'])
            ->whereNotNull('last_login')
            ->when(($startDate && $endDate), function (Builder $query) use ($startDate, $endDate) {
                $query->whereBetween('users.created_at', [$startDate, $endDate]);
            })
            ->when($gender, function (Builder $query) use ($gender) {
                $query->where('users.gender', $gender);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('users.first_name', 'like', '%' . $search . '%');
                $query->orWhere('users.id', 'like', '%' . $search . '%');
                $query->orWhere('users.practice_number', 'like', '%' . $search . '%');
                $query->orWhere('users.description', 'like', '%' . $search . '%');
            })
            ->when($roleID, function ($query) use ($roleID) {
                $query->whereHas('roles', function ($query) use ($roleID) {
                    $query->where('id', $roleID);
                });
            })
            ->orderBy($orderBy, 'desc');
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    //new reports
    public function getCollectionSheetReport($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $loanProductID = $data['loan_product_id'] ?? '';
        $loanOfficerID = $data['loan_officer_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = LoanRepaymentSchedule::with(['loan', 'loan.loanOfficer', 'loan.client', 'loan.product', 'loan.branch'])
            ->when($startDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('due_date', [$startDate, $endDate]);
            })
            ->whereHas('loan', function (Builder $query) use ($branchID, $loanOfficerID, $loanProductID, $ageFrom, $ageTo, $gender) {
                $query->when($branchID, function ($query) use ($branchID) {
                    $query->where('branch_id', $branchID);
                })
                    ->when($loanOfficerID, function ($query) use ($loanOfficerID) {
                        $query->where('loan_officer_id', $loanOfficerID);
                    })
                    ->when($loanProductID, function ($query) use ($loanProductID) {
                        $query->where('loans.loan_product_id', $loanProductID);
                    })
                    ->where('loans.status', 'active');
            })
            ->whereHas('loan.client', function (Builder $query) use ($ageFrom, $ageTo, $gender) {
                $query->when($gender, function ($query) use ($gender) {
                    $query->where('clients.gender', $gender);
                })
                    ->when($ageFrom && !$ageTo, function ($query) use ($ageFrom) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE())=?', [$ageFrom]);
                    })
                    ->when($ageFrom && $ageTo, function ($query) use ($ageFrom, $ageTo) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE()) BETWEEN ? and ?', [$ageFrom, $ageTo]);
                    });
            });
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    public function getLoanRepaymentReport($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $loanProductID = $data['loan_product_id'] ?? '';
        $loanOfficerID = $data['loan_officer_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = LoanTransaction::with(['loan', 'loan.loanOfficer', 'loan.client', 'loan.product', 'loan.branch', 'paymentDetail', 'paymentDetail.payment_type'])
            ->where('loan_transaction_type_id', LoanTransactionType::where('name', 'Repayment')->first()->id)
            ->when($startDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('submitted_on', [$startDate, $endDate]);
            })
            ->whereHas('loan', function (Builder $query) use ($branchID, $loanOfficerID, $loanProductID, $ageFrom, $ageTo, $gender) {
                $query->when($branchID, function ($query) use ($branchID) {
                    $query->where('branch_id', $branchID);
                })
                    ->when($loanOfficerID, function ($query) use ($loanOfficerID) {
                        $query->where('loan_officer_id', $loanOfficerID);
                    })
                    ->when($loanProductID, function ($query) use ($loanProductID) {
                        $query->where('loans.loan_product_id', $loanProductID);
                    });
            })
            ->whereHas('loan.client', function (Builder $query) use ($ageFrom, $ageTo, $gender) {
                $query->when($gender, function ($query) use ($gender) {
                    $query->where('clients.gender', $gender);
                })
                    ->when($ageFrom && !$ageTo, function ($query) use ($ageFrom) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE())=?', [$ageFrom]);
                    })
                    ->when($ageFrom && $ageTo, function ($query) use ($ageFrom, $ageTo) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE()) BETWEEN ? and ?', [$ageFrom, $ageTo]);
                    });
            });
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    public function getLoanExpectedRepaymentReport($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $loanProductID = $data['loan_product_id'] ?? '';
        $loanOfficerID = $data['loan_officer_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = DB::table("loan_repayment_schedules")
            ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
            ->join("clients", "loans.client_id", "clients.id")
            ->join("branches", "loans.branch_id", "branches.id")
            ->when($startDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('loan_repayment_schedules.due_date', [$startDate, $endDate]);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('loans.branch_id', $branchID);
            })
            ->when($loanProductID, function ($query) use ($loanProductID) {
                $query->where('loans.loan_product_id', $loanProductID);
            })
            ->when($loanOfficerID, function ($query) use ($loanOfficerID) {
                $query->where('loans.loan_officer_id', $loanOfficerID);
            })
            ->when($gender, function ($query) use ($gender) {
                $query->where('clients.gender', $gender);
            })
            ->when($ageFrom && !$ageTo, function ($query) use ($ageFrom) {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE())=?', [$ageFrom]);
            })
            ->when($ageFrom && $ageTo, function ($query) use ($ageFrom, $ageTo) {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE()) BETWEEN ? and ?', [$ageFrom, $ageTo]);
            })
            ->where('loans.status', 'active')
            ->selectRaw("branches.name branch,loans.branch_id,coalesce(sum(loan_repayment_schedules.principal-loan_repayment_schedules.principal_written_off_derived),0) principal,coalesce(sum(loan_repayment_schedules.interest-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.interest_waived_derived),0) interest,coalesce(sum(loan_repayment_schedules.fees-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.fees_waived_derived),0) fees,coalesce(sum(loan_repayment_schedules.penalties-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.penalties_waived_derived),0) penalties,coalesce(sum(loan_repayment_schedules.principal_repaid_derived),0) principal_repaid_derived,coalesce(sum(loan_repayment_schedules.interest_repaid_derived),0) interest_repaid_derived,coalesce(sum(loan_repayment_schedules.fees_repaid_derived),0) fees_repaid_derived,coalesce(sum(loan_repayment_schedules.penalties_repaid_derived),0) penalties_repaid_derived,coalesce(sum(loan_repayment_schedules.total_due),0) total_due,coalesce(sum(loan_repayment_schedules.total),0) total,coalesce(sum(loan_repayment_schedules.balance),0) balance")
            ->groupBy('branches.id');
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    public function getArrearsReport($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $loanProductID = $data['loan_product_id'] ?? '';
        $loanOfficerID = $data['loan_officer_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = Loan::with(['loanOfficer', 'client', 'product', 'branch', 'schedules'])
            ->whereHas('schedules', function (Builder $query) use ($endDate) {
                $query->where('due_date', '<=', $endDate)
                    ->where('total_due', '>', 0);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('branch_id', $branchID);
            })
            ->when($loanOfficerID, function ($query) use ($loanOfficerID) {
                $query->where('loan_officer_id', $loanOfficerID);
            })
            ->when($loanProductID, function ($query) use ($loanProductID) {
                $query->where('loans.loan_product_id', $loanProductID);
            })
            ->where('loans.status', 'active')
            ->whereHas('client', function (Builder $query) use ($ageFrom, $ageTo, $gender) {
                $query->when($gender, function ($query) use ($gender) {
                    $query->where('clients.gender', $gender);
                })
                    ->when($ageFrom && !$ageTo, function ($query) use ($ageFrom) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE())=?', [$ageFrom]);
                    })
                    ->when($ageFrom && $ageTo, function ($query) use ($ageFrom, $ageTo) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE()) BETWEEN ? and ?', [$ageFrom, $ageTo]);
                    });
            });
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        $results->transform(function ($loan) {
            $balance = $loan->principal;
            //arrears
            $arrearsDays = 0;
            $arrearsAmount = 0;
            $timelyRepayments = 0;
            $principalOverdue = 0;
            $interestOverdue = 0;
            $feesOverdue = 0;
            $penaltiesOverdue = 0;
            $totalDueRepayments = $loan->schedules->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->count();
            $arrearsLastSchedule = $loan->schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
            if (Carbon::today()->lessThan(Carbon::parse($loan->expected_maturity_date))) {
                $loan->remaining_days = Carbon::today()->diffInDays(Carbon::parse($loan->expected_maturity_date));
            } else {
                $loan->remaining_days = 0;
            }
            if ($lastPayment = LoanTransaction::where('loan_id', $loan->id)->where('loan_transaction_type_id', LoanTransactionType::where('name', 'Repayment')->first()->id)->orderBy('submitted_on', 'desc')->first()) {
                $loan->last_payment_date = $lastPayment->submitted_on;
                $loan->days_since_last_payment = Carbon::today()->diffInDays(Carbon::parse($lastPayment->submitted_on));
                $loan->last_payment_amount = $lastPayment->amount;
            } else {
                $loan->last_payment_date = '-';
                $loan->days_since_last_payment = '-';
                $loan->last_payment_amount = 0;
            }
            if (!empty($arrearsLastSchedule)) {
                $overdueSchedules = $loan->schedules->where('due_date', '<=', $arrearsLastSchedule->due_date);
                $principalOverdue = $overdueSchedules->sum('principal') - $overdueSchedules->sum('principal_written_off_derived') - $overdueSchedules->sum('principal_repaid_derived');
                $interestOverdue = $overdueSchedules->sum('interest') - $overdueSchedules->sum('interest_written_off_derived') - $overdueSchedules->sum('interest_repaid_derived') - $overdueSchedules->sum('interest_waived_derived');
                $feesOverdue = $overdueSchedules->sum('fees') - $overdueSchedules->sum('fees_written_off_derived') - $overdueSchedules->sum('fees_repaid_derived') - $overdueSchedules->sum('fees_waived_derived');
                $penaltiesOverdue = $overdueSchedules->sum('penalties') - $overdueSchedules->sum('penalties_written_off_derived') - $overdueSchedules->sum('penalties_repaid_derived') - $overdueSchedules->sum('penalties_waived_derived');
                $arrearsDays = $arrearsDays + Carbon::today()->diffInDays(Carbon::parse($overdueSchedules->sortBy('due_date')->first()->due_date));
            }
            $loan->schedules->transform(function ($item) use (&$balance, &$arrearsDays, &$arrearsAmount, &$timelyRepayments) {
                $item->total = $item->principal - $item->principal_written_off_derived + $item->interest - $item->interest_written_off_derived - $item->interest_waived_derived + $item->fees - $item->fees_written_off_derived - $item->fees_waived_derived + $item->penalties - $item->penalties_written_off_derived - $item->penalties_waived_derived;
                $item->total_paid = $item->principal_repaid_derived + $item->interest_repaid_derived + $item->fees_repaid_derived + $item->penalties_repaid_derived;
                if ($item->total_due <= 0) {
                    if (Carbon::parse($item->paid_by_date)->greaterThan(Carbon::parse($item->due_date))) {
                        $item->late_payment = true;
                        $arrearsAmount += $item->total_due;
                    } else {
                        $timelyRepayments++;
                        $item->late_payment = false;
                    }
                } else {
                    if (Carbon::today()->greaterThan(Carbon::parse($item->due_date))) {
                        $item->late_payment = true;
                        $arrearsAmount += $item->total_due;
                    } else {
                        $item->late_payment = false;
                    }
                }
                $balance = $balance - $item->principal - $item->principal_written_off_derived;
                $item->balance = $balance;
                $item->days = Carbon::parse($item->due_date)->diffInDays(Carbon::parse($item->from_date));
                return $item;
            });
            if ($totalDueRepayments > 0) {
                $timelyRepayments = round($timelyRepayments * 100 / $totalDueRepayments);
            }
            $loan->timely_repayments = $timelyRepayments;
            $loan->arrears_days = $arrearsDays;
            $loan->arrears_amount = $arrearsAmount;
            $loan->principal_overdue = $principalOverdue;
            $loan->interest_overdue = $interestOverdue;
            $loan->fees_overdue = $feesOverdue;
            $loan->penalties_overdue = $penaltiesOverdue;
            $loan->percentage_overdue = $loan->total_disbursed_derived > 0 ? round($arrearsAmount * 100 / $loan->total_disbursed_derived, 2) : 0;
            return $loan;
        });
        return $results;
    }

    public function getDisbursementReport($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $loanProductID = $data['loan_product_id'] ?? '';
        $loanOfficerID = $data['loan_officer_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = Loan::with(['loanOfficer', 'client', 'product', 'branch', 'schedules', 'purpose', 'fund'])
            ->when($startDate, function (Builder $query) use ($startDate, $endDate) {
                $query->whereBetween('disbursed_on_date', [$startDate, $endDate]);
            })
            ->when($branchID, function ($query) use ($branchID) {
                $query->where('branch_id', $branchID);
            })
            ->when($loanOfficerID, function ($query) use ($loanOfficerID) {
                $query->where('loan_officer_id', $loanOfficerID);
            })
            ->when($loanProductID, function ($query) use ($loanProductID) {
                $query->where('loans.loan_product_id', $loanProductID);
            })
            ->whereHas('client', function (Builder $query) use ($ageFrom, $ageTo, $gender) {
                $query->when($gender, function ($query) use ($gender) {
                    $query->where('clients.gender', $gender);
                })
                    ->when($ageFrom && !$ageTo, function ($query) use ($ageFrom) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE())=?', [$ageFrom]);
                    })
                    ->when($ageFrom && $ageTo, function ($query) use ($ageFrom, $ageTo) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE()) BETWEEN ? and ?', [$ageFrom, $ageTo]);
                    });
            });
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        $results->transform(function ($loan) {
            $balance = $loan->principal;
            //arrears
            $arrearsDays = 0;
            $arrearsAmount = 0;
            $timelyRepayments = 0;
            $principalOverdue = 0;
            $interestOverdue = 0;
            $feesOverdue = 0;
            $penaltiesOverdue = 0;
            $totalDueRepayments = $loan->schedules->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->count();
            $arrearsLastSchedule = $loan->schedules->sortByDesc('due_date')->where('due_date', '<', date("Y-m-d"))->where('total_due', '>', 0)->first();
            if (Carbon::today()->lessThan(Carbon::parse($loan->expected_maturity_date))) {
                $loan->remaining_days = Carbon::today()->diffInDays(Carbon::parse($loan->expected_maturity_date));
            } else {
                $loan->remaining_days = 0;
            }
            if ($lastPayment = LoanTransaction::where('loan_id', $loan->id)->where('loan_transaction_type_id', LoanTransactionType::where('name', 'Repayment')->first()->id)->orderBy('submitted_on', 'desc')->first()) {
                $loan->last_payment_date = $lastPayment->submitted_on;
                $loan->days_since_last_payment = Carbon::today()->diffInDays(Carbon::parse($lastPayment->submitted_on));
                $loan->last_payment_amount = $lastPayment->amount;
            } else {
                $loan->last_payment_date = '-';
                $loan->days_since_last_payment = '-';
                $loan->last_payment_amount = 0;
            }
            if (!empty($arrearsLastSchedule)) {
                $overdueSchedules = $loan->schedules->where('due_date', '<=', $arrearsLastSchedule->due_date);
                $principalOverdue = $overdueSchedules->sum('principal') - $overdueSchedules->sum('principal_written_off_derived') - $overdueSchedules->sum('principal_repaid_derived');
                $interestOverdue = $overdueSchedules->sum('interest') - $overdueSchedules->sum('interest_written_off_derived') - $overdueSchedules->sum('interest_repaid_derived') - $overdueSchedules->sum('interest_waived_derived');
                $feesOverdue = $overdueSchedules->sum('fees') - $overdueSchedules->sum('fees_written_off_derived') - $overdueSchedules->sum('fees_repaid_derived') - $overdueSchedules->sum('fees_waived_derived');
                $penaltiesOverdue = $overdueSchedules->sum('penalties') - $overdueSchedules->sum('penalties_written_off_derived') - $overdueSchedules->sum('penalties_repaid_derived') - $overdueSchedules->sum('penalties_waived_derived');
                $arrearsDays = $arrearsDays + Carbon::today()->diffInDays(Carbon::parse($overdueSchedules->sortBy('due_date')->first()->due_date));
            }
            $loan->schedules->transform(function ($item) use (&$balance, &$arrearsDays, &$arrearsAmount, &$timelyRepayments) {
                $item->total = $item->principal - $item->principal_written_off_derived + $item->interest - $item->interest_written_off_derived - $item->interest_waived_derived + $item->fees - $item->fees_written_off_derived - $item->fees_waived_derived + $item->penalties - $item->penalties_written_off_derived - $item->penalties_waived_derived;
                $item->total_paid = $item->principal_repaid_derived + $item->interest_repaid_derived + $item->fees_repaid_derived + $item->penalties_repaid_derived;
                if ($item->total_due <= 0) {
                    if (Carbon::parse($item->paid_by_date)->greaterThan(Carbon::parse($item->due_date))) {
                        $item->late_payment = true;
                        $arrearsAmount += $item->total_due;
                    } else {
                        $timelyRepayments++;
                        $item->late_payment = false;
                    }
                } else {
                    if (Carbon::today()->greaterThan(Carbon::parse($item->due_date))) {
                        $item->late_payment = true;
                        $arrearsAmount += $item->total_due;
                    } else {
                        $item->late_payment = false;
                    }
                }
                $balance = $balance - $item->principal - $item->principal_written_off_derived;
                $item->balance = $balance;
                $item->days = Carbon::parse($item->due_date)->diffInDays(Carbon::parse($item->from_date));
                return $item;
            });
            if ($totalDueRepayments > 0) {
                $timelyRepayments = round($timelyRepayments * 100 / $totalDueRepayments);
            }
            $loan->timely_repayments = $timelyRepayments;
            $loan->arrears_days = $arrearsDays;
            $loan->arrears_amount = $arrearsAmount;
            $loan->principal_overdue = $principalOverdue;
            $loan->interest_overdue = $interestOverdue;
            $loan->fees_overdue = $feesOverdue;
            $loan->penalties_overdue = $penaltiesOverdue;
            $loan->percentage_overdue = $loan->total_disbursed_derived > 0 ? round($arrearsAmount * 100 / $loan->total_disbursed_derived, 2) : 0;
            return $loan;
        });
        return $results;
    }

    public function getSavingsTransactionsReport($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $savingsProductID = $data['savings_product_id'] ?? '';
        $savingsOfficerID = $data['savings_officer_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = SavingsTransaction::with(['savings', 'type', 'savings.savingsOfficer', 'savings.client', 'savings.product', 'savings.branch', 'paymentDetail', 'paymentDetail.payment_type'])
            ->where('savings_transactions.reversed', 0)
            ->when($startDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('submitted_on', [$startDate, $endDate]);
            })
            ->whereHas('savings', function (Builder $query) use ($branchID, $savingsOfficerID, $savingsProductID, $ageFrom, $ageTo, $gender) {
                $query->when($branchID, function ($query) use ($branchID) {
                    $query->where('branch_id', $branchID);
                })
                    ->when($savingsOfficerID, function ($query) use ($savingsOfficerID) {
                        $query->where('savings_officer_id', $savingsOfficerID);
                    })
                    ->when($savingsProductID, function ($query) use ($savingsProductID) {
                        $query->where('savings_product_id', $savingsProductID);
                    });
            })
            ->whereHas('savings.client', function (Builder $query) use ($ageFrom, $ageTo, $gender) {
                $query->when($gender, function ($query) use ($gender) {
                    $query->where('clients.gender', $gender);
                })
                    ->when($ageFrom && !$ageTo, function ($query) use ($ageFrom) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE())=?', [$ageFrom]);
                    })
                    ->when($ageFrom && $ageTo, function ($query) use ($ageFrom, $ageTo) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE()) BETWEEN ? and ?', [$ageFrom, $ageTo]);
                    });
            });
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    public function getSavingsStatementReport($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $savingsProductID = $data['savings_product_id'] ?? '';
        $savingsOfficerID = $data['savings_officer_id'] ?? '';
        $savingsID = $data['savings_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = SavingsTransaction::with(['savings', 'type', 'savings.savingsOfficer', 'savings.client', 'savings.product', 'savings.branch', 'paymentDetail', 'paymentDetail.payment_type'])
            ->where('savings_transactions.reversed', 0)
            ->when($startDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('submitted_on', [$startDate, $endDate]);
            })
            ->when($savingsID, function ($query) use ($savingsID) {
                $query->where('savings_id', $savingsID);
            })
            ->whereHas('savings', function (Builder $query) use ($branchID, $savingsOfficerID, $savingsProductID, $ageFrom, $ageTo, $gender) {
                $query->when($branchID, function ($query) use ($branchID) {
                    $query->where('branch_id', $branchID);
                })
                    ->when($savingsOfficerID, function ($query) use ($savingsOfficerID) {
                        $query->where('savings_officer_id', $savingsOfficerID);
                    })
                    ->when($savingsProductID, function ($query) use ($savingsProductID) {
                        $query->where('savings_product_id', $savingsProductID);
                    });
            })
            ->whereHas('savings.client', function (Builder $query) use ($ageFrom, $ageTo, $gender) {
                $query->when($gender, function ($query) use ($gender) {
                    $query->where('clients.gender', $gender);
                })
                    ->when($ageFrom && !$ageTo, function ($query) use ($ageFrom) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE())=?', [$ageFrom]);
                    })
                    ->when($ageFrom && $ageTo, function ($query) use ($ageFrom, $ageTo) {
                        $query->whereRaw('TIMESTAMPDIFF(YEAR, clients.dob, CURDATE()) BETWEEN ? and ?', [$ageFrom, $ageTo]);
                    });
            });
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    public function getSavingsBalanceReport($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $savingsProductID = $data['savings_product_id'] ?? '';
        $savingsOfficerID = $data['savings_officer_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = DB::table("savings_transactions")
            ->join("savings", "savings_transactions.savings_id", "savings.id")
            ->join("savings_products", "savings.savings_product_id", "savings_products.id")
            ->join("branches", "savings.branch_id", "branches.id")
            ->leftJoin("users", "savings.savings_officer_id", "users.id")
            ->when($startDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('savings_transactions.submitted_on', [$startDate, $endDate]);
            })->when($branchID, function ($query) use ($branchID) {
                $query->where('savings.branch_id', $branchID);
            })
            ->when($savingsProductID, function ($query) use ($savingsProductID) {
                $query->where('savings.savings_product_id', $savingsProductID);
            })
            ->when($savingsOfficerID, function ($query) use ($savingsOfficerID) {
                $query->where('savings.savings_officer_id', $savingsOfficerID);
            })
            ->selectRaw("concat(users.first_name,' ',users.last_name) savings_officer,branches.name branch,savings_products.name savings_product, credit, debit,savings_transactions.submitted_on");
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }

    public function getSavingsAccounts($data = [])
    {
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';
        $savingsProductID = $data['savings_product_id'] ?? '';
        $savingsOfficerID = $data['savings_officer_id'] ?? '';
        $ageFrom = $data['age_from'] ?? '';
        $ageTo = $data['age_to'] ?? '';
        $gender = $data['gender'] ?? '';
        $branchID = $data['branch_id'] ?? '';
        $paginate = $data['paginate'] ?? false;
        $query = DB::table("savings")
            ->leftJoin("savings_transactions", "savings_transactions.savings_id", "savings.id")
            ->join("savings_products", "savings.savings_product_id", "savings_products.id")
            ->join("branches", "savings.branch_id", "branches.id")
            ->join("clients", "savings.client_id", "clients.id")
            ->leftJoin("users", "savings.savings_officer_id", "users.id")
            ->when($startDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('savings.submitted_on_date', [$startDate, $endDate]);
            })->when($branchID, function ($query) use ($branchID) {
                $query->where('savings.branch_id', $branchID);
            })
            ->when($savingsOfficerID, function ($query) use ($savingsOfficerID) {
                $query->where('savings.savings_officer_id', $savingsOfficerID);
            })
            ->when($savingsProductID, function ($query) use ($savingsProductID) {
                $query->where('savings.savings_product_id', $savingsProductID);
            })
            ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) savings_officer,branches.name branch,savings_products.name product,clients.gender,sum(savings_transactions.credit) credit,sum(savings_transactions.debit) debit,savings.balance_derived balance,savings.submitted_on_date,savings.id id,savings.client_id,savings.savings_officer_id");
        if (!empty($paginate)) {
            $results = $query->paginate();
        } else {
            $results = $query->get();
        }
        return $results;
    }
}
