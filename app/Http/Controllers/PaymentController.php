<?php

namespace App\Http\Controllers;
use App\Models\Payement;
class PaymentController extends Controller
{
    public function index() {
        return Payment::all();
    }

    public function store(Request $request) {
        return Payment::create($request->all());
    }

    public function show(Payment $payment) {
        return $payment;
    }

    public function update(Request $request, Payment $payment) {
        $payment->update($request->all());
        return $payment;
    }

    public function destroy(Payment $payment) {
        $payment->delete();
        return response()->noContent();
    }
}
