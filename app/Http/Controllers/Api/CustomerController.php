<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use App\Models\Customer;
use App\Enums\AddressType;
use App\Enums\CustomerStatus;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerListResource;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search', false);
        $perPage = request('per_page', 10);
        $sortField = request('sort_field', 'updated_at');
        $sortDirection = request('sort_direction', 'desc');

        $query = Customer::query()
            ->orderBy("customers.$sortField", $sortDirection);

            if ($search) {
                $query
                ->join('users', 'customers.user_id', '=', 'users.id')
                ->whereRaw("CONCAT(first_name, ' ', last_name) like ?", "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            }

            $paginator = $query->paginate($perPage);

        return CustomerListResource::collection($paginator);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customerData = $request->validated();
        $customerData['updated_by'] = $request->user()->id;
        $customerData['status'] = $customerData['status'] ? CustomerStatus::Active : CustomerStatus::Disabled;
        $shippingData = $customerData['shippingAddress'];
        $billingData = $customerData['billingAddress'];

        $customer->update($customerData);

        if ($customer->shippingAddress) {
            $customer->shippingAddress->update($shippingData);
        } else {
            $shippingData['customer_id'] = $customer->user_id;
            $shippingData['type'] = AddressType::Shipping;
            CustomerAddress::create($shippingData);
        }

        if ($customer->billingAddress) {
            $customer->billingAddress->update($billingData);
        } else {
            $billingData['customer_id'] = $customer->user_id;
            $billingData['type'] = AddressType::Billing;
            CustomerAddress::create($billingData);
        }

        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->noContent();
    }

    public function countries()
    {
        return CountryResource::collection(Country::query()->orderBy('name', 'asc')->get());
    }
}