<?php

namespace App\Services\V1;

use App\Models\Customer;
use App\Services\BaseQuery;

class CustomerQuery extends BaseQuery
{
    const EXPECTED_PARAMS = [
        "name",
        "city",
        "state",
        "type",

        "page" // for pagination
    ];

    public function __construct()
    {
        parent::__construct(new Customer(), ["includeInvoices"]);
    }
}
