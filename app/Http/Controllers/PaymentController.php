<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\ActivityLogger;
use App\Services\TransactionCalculator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    private array $methods = ['DANA','GoPay','OVO','Bank Jago','BRI','QRIS','ShopeePay','Allo Bank','Lainnya'];

    public function store(Request $request, Transaction $transaction, TransactionCalculator $calculator, ActivityLogger $logger)
    {
        $data = $this->validated($request);
        $amount = round((float)$data['amount'],2);
        $remaining = (float)$transaction->remaining_amount;
        if ($amount > $remaining && ! $request->boolean('confirm_overpayment')) {
            return back()->withInput()->with('error','Nominal melebihi sisa pembayaran. Centang konfirmasi overpayment untuk melanjutkan.');
        }
        if ($transaction->documents()->where('type','receipt')->exists() && ! $request->boolean('confirm_finalized')) {
            return back()->withInput()->with('error','Receipt sudah pernah dibuat. Centang konfirmasi perubahan pembayaran untuk melanjutkan.');
        }

        $payment = $transaction->payments()->create([
            'label'=>$data['label'] ?? null,
            'method'=>$data['method'],
            'custom_method'=>$data['method']==='Lainnya' ? ($data['custom_method'] ?? null) : null,
            'amount'=>$amount,
            'note'=>$data['note'] ?? null,
        ]);
        $calculator->recalculate($transaction);
        $logger->log('payment.created',$transaction,'Pembayaran ditambahkan.',null,['payment_id'=>$payment->id,'amount'=>$amount,'method'=>$payment->methodLabel()]);
        return back()->with('success','Pembayaran berhasil dicatat.');
    }

    public function edit(Transaction $transaction, Payment $payment)
    {
        abort_unless($payment->transaction_id === $transaction->id,404);
        return view('payments.edit',['transaction'=>$transaction,'payment'=>$payment,'methods'=>$this->methods]);
    }

    public function update(Request $request, Transaction $transaction, Payment $payment, TransactionCalculator $calculator, ActivityLogger $logger)
    {
        abort_unless($payment->transaction_id === $transaction->id,404);
        if ($transaction->documents()->where('type','receipt')->exists() && ! $request->boolean('confirm_finalized')) {
            return back()->withInput()->with('error','Receipt sudah pernah dibuat. Konfirmasi perubahan pembayaran wajib dicentang.');
        }
        $data = $this->validated($request);
        $otherPaid = (float) $transaction->payments()->where('id','!=',$payment->id)->sum('amount');
        $newTotalPaid = $otherPaid + (float) $data['amount'];
        if ($newTotalPaid > (float) $transaction->grand_total && ! $request->boolean('confirm_overpayment')) {
            return back()->withInput()->with('error','Nominal perubahan menyebabkan overpayment. Centang konfirmasi overpayment untuk melanjutkan.');
        }
        $before = $payment->toArray();
        $payment->update([
            'label'=>$data['label'] ?? null,
            'method'=>$data['method'],
            'custom_method'=>$data['method']==='Lainnya' ? ($data['custom_method'] ?? null) : null,
            'amount'=>round((float)$data['amount'],2),
            'note'=>$data['note'] ?? null,
        ]);
        $calculator->recalculate($transaction);
        $logger->log('payment.updated',$transaction,'Pembayaran diperbarui.',$before,$payment->fresh()->toArray());
        return redirect()->route('transactions.show',$transaction)->with('success','Pembayaran diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction, Payment $payment, TransactionCalculator $calculator, ActivityLogger $logger)
    {
        abort_unless($payment->transaction_id === $transaction->id,404);
        if ($transaction->documents()->where('type','receipt')->exists() && ! $request->boolean('confirm_finalized')) {
            return back()->with('error','Receipt sudah pernah dibuat. Penghapusan pembayaran membutuhkan konfirmasi eksplisit.');
        }
        $before = $payment->toArray();
        $payment->delete();
        $calculator->recalculate($transaction);
        $logger->log('payment.deleted',$transaction,'Pembayaran dihapus.',$before,null);
        return back()->with('success','Pembayaran dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label'=>['nullable','string','max:80'],
            'method'=>['required',Rule::in($this->methods)],
            'custom_method'=>['nullable','required_if:method,Lainnya','string','max:80'],
            'amount'=>['required','numeric','gt:0','max:999999999999'],
            'note'=>['nullable','string','max:1000'],
        ]);
    }
}
