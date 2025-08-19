<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\DetailCustomerResource;
use App\Http\Resources\CustomerDetailResource;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        $options['key'] = 'customers';
        return CustomerResource::collection($customers);
    }

    public function show($id)
    {
        $customer = Customer::with('alamat')->findOrFail($id);
        return new DetailCustomerResource($customer);
    }

    public function store(Request $request)
    {
        return response()->json($request->all());
        // $validated = $request->validate([
        //     'nama' => ['required', 'max:255'],
        //     'email' => ['required'],
        //     'no_hp' => ['required','max:255'],
        //     'gender' => ['required', 'max:255'],
        // ]);

        // $customer = Customer::create($request->all());

        // return new CustomerDetailResource($customer);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => ['required', 'max:255'],
            'email' => ['required'],
            'no_hp' => ['required', 'max:255'],
            'gender' => ['required', 'max:255'],
        ]);
        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return new CustomerDetailResource($customer);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return new CustomerDetailResource($customer);
    }

    public function search($nama)
    {
        $customer = Customer::where('nama', 'like', '%' . $nama . '%')->get();

        // if($name != ''){
        // }
        // $customer = Customer::get();
        // $options['key'] = 'customers';
        return CustomerResource::collection($customer);
        // $pageLength = request('pageLength') ?? 4;
        // $customers = Customer::filtered();
        // return response()->json($customers->paginate($pageLength), 200);


    }
}
