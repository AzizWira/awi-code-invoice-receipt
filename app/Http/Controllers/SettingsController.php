<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('settings.edit',['settings'=>Setting::map()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'brand_name'=>['required','string','max:120'],
            'signer_name'=>['nullable','string','max:120'],
            'business_whatsapp'=>['nullable','string','max:40'],
            'business_email'=>['nullable','email','max:180'],
            'business_address'=>['nullable','string','max:1000'],
            'invoice_footer'=>['nullable','string','max:3000'],
            'receipt_footer'=>['nullable','string','max:3000'],
            'whatsapp_invoice_template'=>['nullable','string','max:6000'],
            'whatsapp_receipt_template'=>['nullable','string','max:6000'],
            'logo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'signature'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
        ]);

        foreach (['brand_name','signer_name','business_whatsapp','business_email','business_address','invoice_footer','receipt_footer','whatsapp_invoice_template','whatsapp_receipt_template'] as $key) {
            Setting::put($key,$data[$key] ?? null);
        }

        if ($request->hasFile('logo')) Setting::put('logo_path',$this->storeImage($request->file('logo'),'logo'));
        if ($request->hasFile('signature')) Setting::put('signature_path',$this->storeImage($request->file('signature'),'signature'));

        return back()->with('success','Pengaturan berhasil disimpan. Dokumen lama tetap memakai branding snapshot saat dibuat.');
    }

    private function storeImage($file, string $prefix): string
    {
        $extension = strtolower($file->extension() ?: 'bin');
        $name = $prefix.'-'.now()->format('YmdHis').'-'.Str::lower(Str::random(8)).'.'.$extension;
        $directory = public_path('uploads/branding');
        if (! is_dir($directory)) mkdir($directory,0755,true);
        $file->move($directory,$name);
        return 'uploads/branding/'.$name;
    }
}
