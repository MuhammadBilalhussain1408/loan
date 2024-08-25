<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class LoanFile extends Model
{
    protected $fillable = [];
    public $table = "loan_files";

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }
}
