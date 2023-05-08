<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // For security purposes (indicate what columns are editable)
    protected $fillable = [
        "id",
        "name",
        "type",
        "email",
        "adress",
        "city",
        "state",
        "postal_code",
    ];

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }
}
