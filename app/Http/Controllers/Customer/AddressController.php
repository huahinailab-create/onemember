<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Services\CustomerIdentity\AddressBookService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * CUSTOMER-001B — the customer's address book. Every route is behind the
 * `customer` guard, and every address is resolved through the signed-in
 * customer's own relation — one customer can never see or touch another
 * customer's addresses, and merchants have no route into this controller.
 */
class AddressController extends Controller
{
    public function __construct(private AddressBookService $book)
    {
    }

    public function index(Request $request)
    {
        $customer = $request->user('customer');
        $search   = trim((string) $request->query('q', ''));

        $addresses = $customer->addresses()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $like = '%'.$search.'%';
                    $q->where('label', 'like', $like)
                        ->orWhere('recipient_name', 'like', $like)
                        ->orWhere('line1', 'like', $like)
                        ->orWhere('admin_area_1', 'like', $like)
                        ->orWhere('admin_area_2', 'like', $like)
                        ->orWhere('admin_area_3', 'like', $like);
                });
            })
            ->orderByDesc('is_default')
            ->orderByDesc('is_active')
            ->orderByDesc('updated_at')
            ->get();

        return view('customer.addresses.index', [
            'customer'  => $customer,
            'addresses' => $addresses,
            'search'    => $search,
        ]);
    }

    public function create(Request $request)
    {
        return view('customer.addresses.form', [
            'address' => null,
            'country' => $this->country($request->query('country'), $request->user('customer')->country),
        ]);
    }

    public function store(Request $request)
    {
        $customer = $request->user('customer');

        if ($customer->addresses()->count() >= config('customer_address.max_addresses')) {
            throw ValidationException::withMessages(['label' => __('customer_address.limit_reached')]);
        }

        $data = $request->validate($this->book->rulesFor($request->input('country')));
        $this->book->create($customer, $data);

        return redirect()->route('customer.addresses.index')
            ->with('status', __('customer_address.saved'));
    }

    public function edit(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($request, $address);

        return view('customer.addresses.form', [
            'address' => $address,
            'country' => $this->country($request->query('country'), $address->country),
        ]);
    }

    public function update(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($request, $address);

        $data = $request->validate($this->book->rulesFor($request->input('country')));
        $this->book->update($address, $data);

        return redirect()->route('customer.addresses.index')
            ->with('status', __('customer_address.saved'));
    }

    public function destroy(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($request, $address);
        $this->book->delete($address);

        return redirect()->route('customer.addresses.index')
            ->with('status', __('customer_address.deleted'));
    }

    public function archive(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($request, $address);
        $this->book->archive($address);

        return redirect()->route('customer.addresses.index')
            ->with('status', __('customer_address.archived'));
    }

    public function restore(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($request, $address);
        $this->book->restore($address);

        return redirect()->route('customer.addresses.index')
            ->with('status', __('customer_address.restored'));
    }

    public function setDefault(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($request, $address);
        $this->book->setDefault($address);

        return redirect()->route('customer.addresses.index')
            ->with('status', __('customer_address.default_set'));
    }

    public function duplicate(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($request, $address);
        $copy = $this->book->duplicate($address);

        return redirect()->route('customer.addresses.edit', $copy)
            ->with('status', __('customer_address.duplicated'));
    }

    /** 404 (not 403) for foreign addresses — never confirm they exist. */
    private function authorizeAddress(Request $request, CustomerAddress $address): void
    {
        abort_unless($address->customer_id === $request->user('customer')->id, 404);
    }

    private function country(?string $requested, ?string $fallback): string
    {
        $countries = array_keys(config('customer_address.countries'));

        foreach ([$requested, $fallback, config('customer_address.default_country')] as $candidate) {
            if (in_array($candidate, $countries, true)) {
                return $candidate;
            }
        }

        return $countries[0];
    }
}
