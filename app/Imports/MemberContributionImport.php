<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\MemberContribution;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Validator;

class MemberContributionImport implements ToCollection
{
    protected $errors = [];

    public function collection(Collection $rows)
    {
        $arr = [];

        foreach ($rows as $index => $row) {
            if ($index > 0) {
                // Validate each row
                $validator = Validator::make($row->toArray(), [
                    '0'     => 'required|numeric',
                    '1'     => 'required|string|max:255',
                    '2'     => 'required|string|max:255',
                    '3'     => 'required|string|max:255',
                    '4'     => 'required|string|max:255',
                    '5'     => 'required|string|max:255',
                    '6'     => 'required',
                    '7'     => 'required',
                    '8'     => 'required',
                    '9'     => 'required',
                ]);

                if ($validator->fails()) {
                    // Collect errors for this row
                    $this->errors[] = $validator->errors()->toArray();
                }
                $member = Member::where('id', $row[0])->first();
                // dd($member, $row[0]);
                // Prepare data for bulk insert
                if ($member) {
                    $arr[] = [
                        'member_id'     => $row[0],
                        'Surname' => $row[1],
                        'name' => $row[2],
                        'gender' => $row[3],
                        'id_no'    => $row[4],
                        'member_category' => $row[5],
                        'basic_salary' => $row[6],
                        'contri_15_per' => $row[7],
                        'contri_30_per' => $row[8],
                        'total_contribution' => $row[9],
                        'balance' => $member->balance + $row[9],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert users in bulk if no errors found
            // if (empty($this->errors)) {
        }
        if (count($arr) > 0) {
            MemberContribution::insert($arr);
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
