<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\BulkStoreInvoiceRequest;
use App\Http\Resources\V1\InvoiceResource;
use Illuminate\Http\Request;
use App\Services\V1\InvoiceQuery;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query_handler = new InvoiceQuery();

        $query = $query_handler->get_user_query($request, InvoiceQuery::EXPECTED_PARAMS);

        if (count($query) === 0) {
            return InvoiceResource::collection(Invoice::paginate());
        }

        if (array_key_exists(InvoiceQuery::ERROR, $query)) {
            return response($query, 400);
        }

        $results = $query_handler->get_query_result($query);

        /* No results for valid query */
        if (array_key_exists(InvoiceQuery::NO_DATA, $results->toArray())) {
            return response($results, 404);
        }

        return InvoiceResource::collection($query_handler->paginate($results));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        //
    }

    public function bulk_store(BulkStoreInvoiceRequest $request)
    {
        Invoice::insert($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return new InvoiceResource($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
