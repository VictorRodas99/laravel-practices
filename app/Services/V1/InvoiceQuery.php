<?php

namespace App\Services\V1;

use App\Models\Invoice;
use App\Services\BaseQuery;

class InvoiceQuery extends BaseQuery
{
    const EXPECTED_PARAMS = [
        "customer" => "customer_id",
        "status"
    ];

    public function __construct()
    {
        parent::__construct(new Invoice());
    }
}
