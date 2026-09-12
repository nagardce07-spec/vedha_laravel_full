<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    // GET /admin/customers
    public function index()
    {
        $customers = Customer::withCount(['likes', 'reviews'])->latest()->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }
}
