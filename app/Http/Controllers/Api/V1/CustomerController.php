<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Customer;
use App\Http\Requests\V1\StoreCustomerRequest;
use App\Http\Requests\V1\UpdateCustomerRequest;
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
        $query_handler = new CustomerQuery();

        $query = $query_handler->get_user_query($request, CustomerQuery::EXPECTED_PARAMS);
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

        $results = $query_handler->get_query_result($query);

        /* No results for valid query */
        if (array_key_exists(CustomerQuery::NO_DATA, $results->toArray())) {
            return response($results, 404);
        }

        if ($include_invoices) {
            $results = $results->with('invoices');
        }

        return new CustomerCollection($query_handler->paginate($results));
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
        if (count(collect($customer)) === 0) {
            return response()->json([
                'message' => 'customer not found, please, provide id'
            ], 402);
        }

        $customer->update($request->all());
        $updated_fields = collect(array_keys($request->all()))
            ->filter(
                fn ($field) => !str_contains($field, '_')
            );

        $final_message = "Customer $customer->id updated successfully!";

        if ($request->method() === 'PATCH') {
            return response()->json([
                'message' => $final_message,
                'updatedFields' => $updated_fields
            ]);
        }

        return response()->json([
            'message' => $final_message
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if (count($customer->toArray()) === 0) {
            return response()->json([
                'message' => 'customer not found, please, provide an id'
            ], 402);
        }

        $given_customer_id = $customer->id;

        Customer::destroy($given_customer_id);

        return response()->json([
            'message' => "Customer $given_customer_id deleted!"
        ]);
    }
}
