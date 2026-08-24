<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items=['Pembuatan Website','Revisi Website','Bug Fix','Tugas / Joki','Desain','Hosting','Domain','Maintenance','Setup / Deploy','Lainnya'];
        foreach($items as $index=>$name){ServiceType::updateOrCreate(['name'=>$name],['is_active'=>true,'sort_order'=>($index+1)*10]);}
    }
}
