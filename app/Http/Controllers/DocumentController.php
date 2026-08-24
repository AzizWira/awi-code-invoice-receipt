<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Setting;
use App\Models\Transaction;
use App\Services\ActivityLogger;
use App\Services\DocumentAssetService;
use App\Services\DocumentNumberService;
use App\Services\DocumentSnapshotService;
use App\Services\IndonesianNumberService;
use App\Support\Money;
use App\Support\Whatsapp;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $type = $request->get('type');
        $status = $request->get('status');

        $documents = Document::query()->with(['transaction.customer'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('document_number','like',"%{$q}%")
                        ->orWhereHas('transaction', function ($t) use ($q) {
                            $t->where('project_name','like',"%{$q}%")
                                ->orWhereHas('customer', fn($c) => $c->where('name','like',"%{$q}%")->orWhere('company_name','like',"%{$q}%"));
                        });
                });
            })
            ->when(in_array($type,['invoice','receipt'],true), fn($q2) => $q2->where('type',$type))
            ->when(in_array($status,['active','revoked'],true), fn($q2) => $q2->where('status',$status))
            ->latest('generated_at')->paginate(15)->withQueryString();

        return view('documents.index', compact('documents','q','type','status'));
    }

    public function generate(Request $request, Transaction $transaction, string $type, DocumentNumberService $numbers, DocumentSnapshotService $snapshots, ActivityLogger $logger)
    {
        abort_unless(in_array($type,['invoice','receipt'],true),404);
        $transaction->load(['items','payments','documents']);
        if (! $transaction->isFinal() || $transaction->transaction_status === 'cancelled') {
            return back()->with('error','Finalkan transaksi terlebih dahulu sebelum membuat dokumen.');
        }
        if ($type === 'receipt' && ! $transaction->isPaid()) {
            return back()->with('error','Receipt hanya dapat dibuat setelah transaksi lunas.');
        }

        $existing = $transaction->documents()->where('type',$type)->first();
        if ($existing) return redirect()->route('documents.show',$existing)->with('info','Dokumen sudah pernah dibuat. Snapshot lama tetap dipertahankan.');

        $token = Str::random(64);
        $document = $transaction->documents()->create([
            'type'=>$type,
            'document_number'=>$numbers->make($type,$transaction->transaction_date),
            'public_token_hash'=>hash('sha256',$token),
            'public_token_encrypted'=>Crypt::encryptString($token),
            'public_token_last4'=>substr($token,-4),
            'status'=>'active',
            'snapshot_json'=>$snapshots->make($transaction),
            'generated_at'=>now(),
            'finalized_at'=>now(),
        ]);
        $logger->log('document.generated',$transaction,ucfirst($type).' dibuat.',null,['document_id'=>$document->id,'document_number'=>$document->document_number]);
        return redirect()->route('documents.show',$document)->with('success',ucfirst($type).' berhasil dibuat.');
    }

    public function show(Document $document, IndonesianNumberService $terbilang, DocumentAssetService $assets)
    {
        $document->load('transaction.customer');
        return view('documents.show', $this->viewData($document,$terbilang,$assets,false));
    }

    public function publicShow(string $token, IndonesianNumberService $terbilang, DocumentAssetService $assets)
    {
        $document = Document::query()->where('public_token_hash',hash('sha256',$token))->firstOrFail();
        abort_unless($document->isActive(),404);
        return view('documents.public', $this->viewData($document,$terbilang,$assets,true));
    }

    public function pdf(Document $document, IndonesianNumberService $terbilang, DocumentAssetService $assets)
    {
        return $this->renderPdf($document, $terbilang, $assets);
    }

    public function publicPdf(string $token, IndonesianNumberService $terbilang, DocumentAssetService $assets)
    {
        $document = Document::query()->where('public_token_hash', hash('sha256', $token))->firstOrFail();
        abort_unless($document->isActive(), 404);
        return $this->renderPdf($document, $terbilang, $assets);
    }

    public function regenerateSnapshot(Document $document, DocumentSnapshotService $snapshots, ActivityLogger $logger)
    {
        $transaction = $document->transaction()->with(['items','payments'])->firstOrFail();
        if ($document->type === 'receipt' && ! $transaction->isPaid()) return back()->with('error','Receipt tidak dapat diperbarui karena transaksi saat ini belum lunas.');
        $document->update(['snapshot_json'=>$snapshots->make($transaction),'generated_at'=>now()]);
        $logger->log('document.snapshot_regenerated',$transaction,'Snapshot dokumen diperbarui.',null,['document_id'=>$document->id]);
        return back()->with('success','Snapshot dokumen diperbarui menggunakan data transaksi terbaru.');
    }

    public function regenerateToken(Document $document, ActivityLogger $logger)
    {
        $token = Str::random(64);
        $document->update([
            'public_token_hash'=>hash('sha256',$token),
            'public_token_encrypted'=>Crypt::encryptString($token),
            'public_token_last4'=>substr($token,-4),
            'status'=>'active',
            'revoked_at'=>null,
        ]);
        $logger->log('document.token_regenerated',$document->transaction,'Token publik dokumen dibuat ulang.',null,['document_id'=>$document->id]);
        return back()->with('success','Link publik lama sudah tidak berlaku. Link baru siap digunakan.');
    }

    public function revoke(Document $document, ActivityLogger $logger)
    {
        $document->update(['status'=>'revoked','revoked_at'=>now()]);
        $logger->log('document.revoked',$document->transaction,'Akses publik dokumen dicabut.',null,['document_id'=>$document->id]);
        return back()->with('success','Akses publik dokumen berhasil dicabut.');
    }

    public function whatsapp(Document $document)
    {
        abort_unless($document->isActive(),404);
        $snapshot = $document->snapshot_json;
        $number = Whatsapp::normalize($snapshot['customer']['whatsapp'] ?? null);
        if (! $number) return back()->with('error','Customer tidak memiliki nomor WhatsApp.');

        $templateKey = $document->type === 'receipt' ? 'whatsapp_receipt_template' : 'whatsapp_invoice_template';
        $default = $document->type === 'receipt'
            ? "Halo Kak {customer},\n\nTerima kasih, pembayaran untuk project {project} telah kami terima sepenuhnya.\n\nBerikut receipt/bukti transaksi dari {brand_name}:\n{public_link}\n\nTotal: {total}\nStatus: Lunas\n\nTerima kasih telah menggunakan jasa {brand_name}."
            : "Halo Kak {customer},\n\nBerikut kami kirimkan invoice untuk project {project}.\n\nNomor Invoice: {document_number}\nTotal: {total}\nSisa Pembayaran: {remaining}\n\nDetail invoice dapat dilihat melalui tautan berikut:\n{public_link}\n\nTerima kasih.\n{brand_name}";
        $template = Setting::value($templateKey,$default);
        $link = route('public.documents.show',['token'=>$document->publicToken()]);
        $replacements = [
            '{customer}'=>$snapshot['customer']['name'] ?? '-',
            '{company}'=>$snapshot['customer']['company_name'] ?? '',
            '{project}'=>$snapshot['transaction']['project_name'] ?? '-',
            '{document_number}'=>$document->document_number,
            '{total}'=>Money::rupiah($snapshot['transaction']['grand_total'] ?? 0),
            '{paid}'=>Money::rupiah($snapshot['transaction']['total_paid'] ?? 0),
            '{remaining}'=>Money::rupiah($snapshot['transaction']['remaining_amount'] ?? 0),
            '{public_link}'=>$link,
            '{brand_name}'=>$snapshot['branding']['brand_name'] ?? 'Awi Code',
        ];
        $message = strtr($template,$replacements);
        return redirect()->away('https://wa.me/'.$number.'?text='.rawurlencode($message));
    }


    private function renderPdf(Document $document, IndonesianNumberService $terbilang, DocumentAssetService $assets)
    {
        $data = $this->viewData($document,$terbilang,$assets,true);
        $html = view('documents.pdf',$data)->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html,'UTF-8');
        $dompdf->setPaper('A4','portrait');
        $dompdf->render();

        $company = $document->snapshot_json['customer']['company_name'] ?: $document->snapshot_json['customer']['name'];
        $safe = Str::slug($company ?: 'customer');
        $filename = $document->document_number.'-'.$safe.'.pdf';
        return response($dompdf->output(),200,[
            'Content-Type'=>'application/pdf',
            'Content-Disposition'=>'attachment; filename="'.$filename.'"',
        ]);
    }

    private function viewData(Document $document, IndonesianNumberService $terbilang, DocumentAssetService $assets, bool $public): array
    {
        $snapshot = $document->snapshot_json;
        $branding = $snapshot['branding'] ?? [];
        return [
            'document'=>$document,
            'snapshot'=>$snapshot,
            'terbilang'=>$terbilang->spell($snapshot['transaction']['grand_total'] ?? 0),
            'logoDataUri'=>$assets->dataUri($branding['logo_path'] ?? null),
            'signatureDataUri'=>$assets->dataUri($branding['signature_path'] ?? null),
            'publicMode'=>$public,
            'publicLink'=>$document->isActive() ? route('public.documents.show',['token'=>$document->publicToken()]) : null,
        ];
    }
}
