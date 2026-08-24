<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Transaction;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $customers = Customer::query()->withCount('transactions')
            ->when(! $request->boolean('archived'), fn ($x) => $x->whereNull('archived_at'))
            ->when($request->boolean('archived'), fn ($x) => $x->whereNotNull('archived_at'))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name','like',"%{$q}%")
                        ->orWhere('company_name','like',"%{$q}%")
                        ->orWhere('whatsapp','like',"%{$q}%")
                        ->orWhere('email','like',"%{$q}%");
                });
            })->latest()->paginate(15)->withQueryString();

        return view('customers.index', compact('customers','q'));
    }

    public function create() { return view('customers.form', ['customer'=>new Customer]); }

    public function store(Request $request, ActivityLogger $logger)
    {
        $data = $this->validated($request);
        $customer = Customer::create($data);
        $logger->log('customer.created', $customer, 'Customer dibuat.', null, $customer->only(['name','company_name','whatsapp','email']));
        return redirect()->route('customers.show',$customer)->with('success','Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $transactions = $customer->transactions()->latest('transaction_date')->paginate(10);
        $customerStats = [
            'count' => $customer->transactions()->count(),
            'value' => (float) $customer->transactions()->sum('grand_total'),
            'paid' => (float) $customer->transactions()->sum('total_paid'),
        ];
        return view('customers.show', compact('customer','transactions','customerStats'));
    }

    public function edit(Customer $customer) { return view('customers.form', compact('customer')); }

    public function update(Request $request, Customer $customer, ActivityLogger $logger)
    {
        $before = $customer->only(['name','company_name','whatsapp','email','note']);
        $customer->update($this->validated($request));
        $logger->log('customer.updated', $customer, 'Customer diperbarui.', $before, $customer->only(array_keys($before)));
        return redirect()->route('customers.show',$customer)->with('success','Customer berhasil diperbarui.');
    }

    public function archive(Customer $customer, ActivityLogger $logger)
    {
        if ($customer->archived_at) {
            return back()->with('info','Customer ini sudah berada di arsip.');
        }

        $customer->update(['archived_at'=>now()]);
        $logger->log('customer.archived', $customer, 'Customer diarsipkan.');
        return redirect()->route('customers.index')->with('success','Customer diarsipkan.');
    }

    public function restore(Customer $customer, ActivityLogger $logger)
    {
        $customer->update(['archived_at'=>null]);
        $logger->log('customer.restored', $customer, 'Customer dipulihkan dari arsip.');
        return back()->with('success','Customer dipulihkan.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        $request->validate([
            'confirm_delete' => ['accepted'],
        ], [
            'confirm_delete.accepted' => 'Konfirmasi penghapusan customer tidak valid.',
        ]);

        $displayName = $customer->company_name ?: $customer->name;
        $transactionIds = $customer->transactions()->pluck('id');
        $transactionCount = $transactionIds->count();

        DB::transaction(function () use ($customer, $transactionIds): void {
            ActivityLog::query()
                ->where('subject_type', Customer::class)
                ->where('subject_id', $customer->id)
                ->delete();

            if ($transactionIds->isNotEmpty()) {
                ActivityLog::query()
                    ->where('subject_type', Transaction::class)
                    ->whereIn('subject_id', $transactionIds)
                    ->delete();

                // Item, pembayaran, dan dokumen mengikuti FK cascade dari transaksi.
                Transaction::query()->whereIn('id', $transactionIds)->delete();
            }

            $customer->delete();
        });

        $suffix = $transactionCount > 0
            ? ' beserta '.$transactionCount.' transaksi dan seluruh data terkait'
            : '';

        return redirect()->route('customers.index')
            ->with('success', 'Customer “'.$displayName.'”'.$suffix.' berhasil dihapus permanen.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'company_name' => ['nullable','string','max:180'],
            'whatsapp' => ['nullable','string','max:32','required_without:email'],
            'email' => ['nullable','email','max:180','required_without:whatsapp'],
            'note' => ['nullable','string','max:3000'],
        ], [
            'whatsapp.required_without' => 'Isi minimal nomor WhatsApp atau email.',
            'email.required_without' => 'Isi minimal email atau nomor WhatsApp.',
        ]);
        foreach (['whatsapp','email','company_name','note'] as $field) {
            if (isset($data[$field]) && trim((string)$data[$field]) === '') $data[$field] = null;
        }
        return $data;
    }
}
