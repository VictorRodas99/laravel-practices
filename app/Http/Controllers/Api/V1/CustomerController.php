<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Customer;
use App\Http\Requests\V1\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\CustomerCollection;
use App\Services\V1\CustomerQuery;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = (new CustomerQuery)->get_user_query($request, CustomerQuery::EXPECTED_PARAMS);
        $include_invoices = $request->query('includeInvoices');

        if (count($query) === 0) {
            return $include_invoices
                ? new CustomerCollection(Customer::with('invoice')->paginate())
                : new CustomerCollection(Customer::paginate()); // This use ResourceClass Collections to transform all given data into a given format
        }

        /* Invalid query  */
        if (array_key_exists(CustomerQuery::ERROR, $query)) {
            return response($query, 400);
        }

        $results = (new CustomerQuery)->get_query_result($query);

        /* No results for valid query */
        if (array_key_exists(CustomerQuery::NO_DATA, $results->toArray())) {
            return response($results, 404);
        }

        if ($include_invoices) {
            $results = $results->with('invoices');
        }

        return new CustomerCollection(CustomerQuery::paginate($results));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        return new CustomerResource(Customer::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $include_invoices = request()->query("includeInvoices");

        if ($include_invoices) {
            return new CustomerResource($customer->loadMissing("invoice"));
        }

        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
