<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, Request $request) {

            if ($request->is('api/v1/invoices/*')) {
                $invoice_id = $request->route('invoice');

                return response()->json([
                    'message' => "Invoice record {$invoice_id} not found!"
                ], 404);
            }

            if ($request->is('api/v1/customers/*')) {
                $customer_id = $request->route('customer');

                return response()->json([
                    'message' => "Customer record {$customer_id} not found!"
                ], 404);
            }

            // Route not found exception
            $final_url = $request->path();

            return response()->json([
                'message' => "The route {$final_url} could not be found"
            ], 404);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
