<?php

namespace App\Imports;


use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;

class MembersImport implements ToModel
{

    public array $args;
    private $currentRow = 0;
    private $headings = [];

    public function __construct(array $args = [])
    {

        $this->args = $args;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        //assume row 0 is headings

        if ($this->currentRow < 1) {
            $this->headings = $row;
            $this->currentRow++;

            return null;
        }
        if (!empty($row[2]) && !empty($row['1']) && !empty($row['3']) && $row[1] !== 'emp_id' && $row[2] !== 'lastname') {
            $member = new Member();
            $member->created_by_id = Auth::id();
            $member->first_name = $row[2];
            $member->middle_name = $row[4];
            $member->last_name = $row[3];
            $member->external_id = $row[1];
            $member->identification_number = $row[5];
            $member->status = 'active';
            $member->save();

            $this->currentRow++;
            return $member;
        }

    }
}
