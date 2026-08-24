<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $invoice="Halo Kak {customer},\n\nBerikut kami kirimkan invoice untuk project {project}.\n\nNomor Invoice: {document_number}\nTotal: {total}\nSisa Pembayaran: {remaining}\n\nDetail invoice dapat dilihat melalui tautan berikut:\n{public_link}\n\nTerima kasih.\n{brand_name}";
        $receipt="Halo Kak {customer},\n\nTerima kasih, pembayaran untuk project {project} telah kami terima sepenuhnya.\n\nBerikut receipt/bukti transaksi dari {brand_name}:\n{public_link}\n\nTotal: {total}\nStatus: Lunas\n\nTerima kasih telah menggunakan jasa {brand_name}.";
        $defaults=[
            'brand_name'=>'Awi Code','logo_path'=>'brand/awi-code-logo.jpg','signer_name'=>'Awi Code','signature_path'=>null,
            'business_whatsapp'=>null,'business_email'=>null,'business_address'=>null,
            'invoice_footer'=>'Terima kasih telah mempercayakan kebutuhan digital Anda kepada Awi Code.',
            'receipt_footer'=>'Terima kasih telah mempercayakan kebutuhan digital Anda kepada Awi Code.',
            'whatsapp_invoice_template'=>$invoice,'whatsapp_receipt_template'=>$receipt,
        ];
        foreach($defaults as $key=>$value){Setting::query()->firstOrCreate(['key'=>$key],['value'=>$value]);}
    }
}
