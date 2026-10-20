<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\ServiceType;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\ActivityLogger;
use App\Services\TransactionCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $status = $request->get('status');
        $workStatus = $request->get('work_status');
        $serviceType = $request->get('service_type');
        $serviceTypeId = $serviceType ? ServiceType::query()->where('route_key', $serviceType)->value('id') : null;
        $sort = $request->get('sort','latest');

        $transactions = Transaction::query()->with(['customer','serviceType','documents'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('project_name','like',"%{$q}%")
                        ->orWhere('internal_code','like',"%{$q}%")
                        ->orWhereHas('customer', function ($c) use ($q) {
                            $c->where('name','like',"%{$q}%")->orWhere('company_name','like',"%{$q}%");
                        })
                        ->orWhereHas('documents', fn($d) => $d->where('document_number','like',"%{$q}%"));
                });
            })
            ->when($status, fn($query) => $query->where('payment_status',$status))
            ->when($workStatus, fn($query) => $query->where('work_status',$workStatus))
            ->when($serviceTypeId, fn($query) => $query->where('service_type_id',$serviceTypeId))
            ->when($sort === 'oldest', fn($q) => $q->oldest('transaction_date'))
            ->when($sort === 'highest', fn($q) => $q->orderByDesc('grand_total'))
            ->when($sort === 'lowest', fn($q) => $q->orderBy('grand_total'))
            ->when(!in_array($sort,['oldest','highest','lowest'],true), fn($q) => $q->latest('transaction_date'))
            ->paginate(15)->withQueryString();

        $serviceTypes = ServiceType::active()->get();
        return view('transactions.index', compact('transactions','serviceTypes','q','status','workStatus','serviceType','sort'));
    }

    public function create(Request $request)
    {
        $selectedCustomerId = $request->filled('customer')
            ? Customer::active()->where('route_key', (string) $request->get('customer'))->value('id')
            : null;

        return view('transactions.form', [
            'transaction' => new Transaction(['transaction_date'=>today(),'work_status'=>'menunggu','progress_percent'=>0,'discount_type'=>null,'discount_value'=>0]),
            'customers' => Customer::active()->orderBy('company_name')->orderBy('name')->get(),
            'serviceTypes' => ServiceType::active()->get(),
            'items' => collect([new TransactionItem(['quantity'=>1,'unit'=>'project','unit_price'=>0])]),
            'acknowledged' => false,
            'selectedCustomerId' => $selectedCustomerId,
        ]);
    }

    public function store(Request $request, TransactionCalculator $calculator, ActivityLogger $logger)
    {
        $validated = $this->validated($request);
        $this->validateDiscountAgainstItems($validated);

        $transaction = DB::transaction(function () use ($request, $validated, $calculator) {
            [$serviceTypeId, $customServiceType] = $this->resolveServiceType($request, $validated);
            $intent = $request->input('intent','draft');
            $status = $intent === 'final' ? 'final' : 'draft';

            $transaction = Transaction::create([
                'internal_code' => 'TRX-'.strtoupper(Str::random(12)),
                'customer_id' => $validated['customer_id'],
                'service_type_id' => $serviceTypeId,
                'custom_service_type' => $customServiceType,
                'project_name' => $validated['project_name'],
                'description' => $validated['description'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'work_status' => $status === 'final' ? 'selesai' : $validated['work_status'],
                'progress_percent' => $status === 'final' ? 100 : $validated['progress_percent'],
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'transaction_status' => $status,
                'internal_note' => $validated['internal_note'] ?? null,
                'finalized_at' => $status === 'final' ? now() : null,
            ]);

            $this->syncItems($transaction, $validated['items']);
            return $calculator->recalculate($transaction);
        });

        $logger->log('transaction.created',$transaction,'Transaksi dibuat.',null,$this->auditSnapshot($transaction));
        return redirect()->route('transactions.show',$transaction)->with('success', $transaction->isFinal() ? 'Transaksi berhasil dibuat dan difinalkan.' : 'Draft transaksi berhasil disimpan.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['customer','serviceType','items','payments','documents']);
        $logs = \App\Models\ActivityLog::query()
            ->where('subject_type', Transaction::class)->where('subject_id',$transaction->id)
            ->latest()->limit(15)->get();
        return view('transactions.show', compact('transaction','logs'));
    }

    public function edit(Request $request, Transaction $transaction)
    {
        $transaction->load(['items','documents']);
        $risky = $transaction->payment_status === 'lunas' || $transaction->documents->isNotEmpty();
        if ($risky && ! $request->boolean('ack')) {
            return view('transactions.edit-warning', compact('transaction'));
        }

        return view('transactions.form', [
            'transaction'=>$transaction,
            'customers'=>Customer::active()->orWhere('id',$transaction->customer_id)->orderBy('company_name')->orderBy('name')->get(),
            'serviceTypes'=>ServiceType::active()->orWhere('id',$transaction->service_type_id)->get(),
            'items'=>$transaction->items,
            'acknowledged'=>$risky,
        ]);
    }

    public function update(Request $request, Transaction $transaction, TransactionCalculator $calculator, ActivityLogger $logger)
    {
        $transaction->load(['items','payments','documents']);
        $validated = $this->validated($request);
        $this->validateDiscountAgainstItems($validated);
        $projectedGrandTotal = $this->projectedGrandTotal($validated);
        if ((float) $transaction->total_paid > $projectedGrandTotal && ! $request->boolean('confirm_overpayment')) {
            return back()->withInput()->with('error', 'Total baru lebih kecil dari pembayaran yang sudah tercatat. Centang konfirmasi overpayment untuk melanjutkan.');
        }
        $before = $this->auditSnapshot($transaction);

        $updated = DB::transaction(function () use ($request,$validated,$transaction,$calculator) {
            [$serviceTypeId, $customServiceType] = $this->resolveServiceType($request,$validated);
            $intent = $request->input('intent', $transaction->transaction_status === 'final' ? 'final' : 'draft');
            $status = $intent === 'final' ? 'final' : 'draft';

            $transaction->update([
                'customer_id'=>$validated['customer_id'],
                'service_type_id'=>$serviceTypeId,
                'custom_service_type'=>$customServiceType,
                'project_name'=>$validated['project_name'],
                'description'=>$validated['description'] ?? null,
                'transaction_date'=>$validated['transaction_date'],
                'work_status'=>$status === 'final' ? 'selesai' : $validated['work_status'],
                'progress_percent'=>$status === 'final' ? 100 : $validated['progress_percent'],
                'discount_type'=>$validated['discount_type'] ?? null,
                'discount_value'=>$validated['discount_value'] ?? 0,
                'transaction_status'=>$status,
                'internal_note'=>$validated['internal_note'] ?? null,
                'finalized_at'=>$status === 'final' ? ($transaction->finalized_at ?: now()) : null,
            ]);
            $this->syncItems($transaction,$validated['items']);
            return $calculator->recalculate($transaction);
        });

        $logger->log('transaction.updated',$updated,'Transaksi diperbarui.',$before,$this->auditSnapshot($updated));
        $message = $updated->documents()->exists()
            ? 'Transaksi diperbarui. Dokumen lama tetap memakai snapshot sebelumnya sampai Anda memilih Perbarui Snapshot.'
            : 'Transaksi berhasil diperbarui.';
        return redirect()->route('transactions.show',$updated)->with('success',$message);
    }

    public function cancel(Transaction $transaction, ActivityLogger $logger)
    {
        $before = $transaction->transaction_status;
        $transaction->update(['transaction_status'=>'cancelled','work_status'=>'dibatalkan']);
        $logger->log('transaction.cancelled',$transaction,'Transaksi dibatalkan.',['transaction_status'=>$before],['transaction_status'=>'cancelled']);
        return back()->with('success','Transaksi dibatalkan.');
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $request->validate([
            'confirm_delete' => ['accepted'],
        ], [
            'confirm_delete.accepted' => 'Konfirmasi penghapusan transaksi tidak valid.',
        ]);

        $projectName = $transaction->project_name;
        $paymentCount = $transaction->payments()->count();
        $documentCount = $transaction->documents()->count();

        DB::transaction(function () use ($transaction): void {
            ActivityLog::query()
                ->where('subject_type', Transaction::class)
                ->where('subject_id', $transaction->id)
                ->delete();

            // transaction_items, payments, dan documents menggunakan FK cascadeOnDelete.
            $transaction->delete();
        });

        $related = [];
        if ($paymentCount > 0) $related[] = $paymentCount.' pembayaran';
        if ($documentCount > 0) $related[] = $documentCount.' dokumen';
        $suffix = $related ? ' beserta '.implode(' dan ', $related).' terkait' : '';

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi “'.$projectName.'”'.$suffix.' berhasil dihapus permanen.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id'=>['required','integer','exists:customers,id'],
            'service_type_id'=>['required','integer','exists:service_types,id'],
            'custom_service_type'=>['nullable','string','max:120'],
            'save_custom_service'=>['nullable','boolean'],
            'project_name'=>['required','string','max:180'],
            'description'=>['nullable','string','max:5000'],
            'transaction_date'=>['required','date'],
            'work_status'=>['required',Rule::in(['menunggu','dikerjakan','revisi','selesai','dibatalkan'])],
            'progress_percent'=>['required','integer','min:0','max:100'],
            'discount_type'=>['nullable',Rule::in(['nominal','percent'])],
            'discount_value'=>['nullable','numeric','min:0'],
            'internal_note'=>['nullable','string','max:5000'],
            'items'=>['required','array','min:1'],
            'items.*.name'=>['required','string','max:180'],
            'items.*.description'=>['nullable','string','max:1500'],
            'items.*.quantity'=>['required','numeric','gt:0','max:999999'],
            'items.*.unit'=>['required','string','max:40'],
            'items.*.unit_price'=>['required','numeric','min:0','max:999999999999'],
        ]);
    }



    private function projectedGrandTotal(array $validated): float
    {
        $subtotal = collect($validated['items'])->sum(fn ($item) => round(round((float) $item['quantity'], 2) * round((float) $item['unit_price'], 2), 2));
        $type = $validated['discount_type'] ?? null;
        $value = (float) ($validated['discount_value'] ?? 0);
        $discount = 0.0;
        if ($type === 'percent') $discount = $subtotal * ($value / 100);
        if ($type === 'nominal') $discount = $value;
        return max(0, round($subtotal - $discount, 2));
    }

    private function validateDiscountAgainstItems(array $validated): void
    {
        $subtotal = collect($validated['items'])->sum(fn ($item) => round(round((float) $item['quantity'], 2) * round((float) $item['unit_price'], 2), 2));
        $type = $validated['discount_type'] ?? null;
        $value = (float) ($validated['discount_value'] ?? 0);

        if ($type === 'percent' && $value > 100) {
            throw ValidationException::withMessages(['discount_value' => 'Diskon persen tidak boleh melebihi 100%.']);
        }
        if ($type === 'nominal' && $value > $subtotal) {
            throw ValidationException::withMessages(['discount_value' => 'Diskon nominal tidak boleh melebihi subtotal transaksi.']);
        }
    }

    private function resolveServiceType(Request $request, array $validated): array
    {
        $selected = ServiceType::findOrFail($validated['service_type_id']);
        if (strtolower($selected->name) !== 'lainnya') return [$selected->id,null];

        $custom = trim((string)($validated['custom_service_type'] ?? ''));
        if ($custom === '') {
            throw ValidationException::withMessages(['custom_service_type' => 'Nama jenis jasa lainnya wajib diisi.']);
        }

        if ($request->boolean('save_custom_service')) {
            $service = ServiceType::firstOrCreate(['name'=>$custom],['is_active'=>true,'sort_order'=>(int)ServiceType::max('sort_order')+10]);
            return [$service->id,null];
        }
        return [$selected->id,$custom];
    }

    private function syncItems(Transaction $transaction, array $items): void
    {
        $transaction->items()->delete();
        foreach (array_values($items) as $index => $item) {
            $qty = round((float)$item['quantity'],2);
            $price = round((float)$item['unit_price'],2);
            $transaction->items()->create([
                'name'=>$item['name'],
                'description'=>$item['description'] ?? null,
                'quantity'=>$qty,
                'unit'=>$item['unit'],
                'unit_price'=>$price,
                'line_total'=>round($qty*$price,2),
                'sort_order'=>$index,
            ]);
        }
    }

    private function auditSnapshot(Transaction $transaction): array
    {
        $transaction->loadMissing(['items','payments']);
        return [
            'project_name'=>$transaction->project_name,
            'transaction_date'=>$transaction->transaction_date?->format('Y-m-d'),
            'transaction_status'=>$transaction->transaction_status,
            'payment_status'=>$transaction->payment_status,
            'subtotal'=>(float)$transaction->subtotal,
            'discount_type'=>$transaction->discount_type,
            'discount_value'=>(float)$transaction->discount_value,
            'grand_total'=>(float)$transaction->grand_total,
            'total_paid'=>(float)$transaction->total_paid,
            'items'=>$transaction->items->map(fn($i)=>[$i->name,(float)$i->quantity,$i->unit,(float)$i->unit_price])->all(),
        ];
    }
}
