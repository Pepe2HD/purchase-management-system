<?php

namespace App\Http\Controllers;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index() {
        $requests = PurchaseRequest::all();
        return view('purchase_requests.index', compact('requests'));
    }

    public function create() {
        return view('purchase_requests.create');
    }

    public function store(Request $request) {
        PurchaseRequest::create($request->all());
        return redirect()->route('purchase_requests.index');
    }

    public function edit($id) {
        $requestItem = PurchaseRequest::findOrFail($id);
        return view('purchase_requests.edit', compact('requestItem'));
    }

    public function update(Request $request, $id) {
        $requestItem = PurchaseRequest::findOrFail($id);
        $requestItem->update($request->all());
        return redirect()->route('purchase_requests.index');
    }

    public function destroy($id) {
        $requestItem = PurchaseRequest::findOrFail($id);
        $requestItem->delete();
        return redirect()->route('purchase_requests.index');
    }
}
