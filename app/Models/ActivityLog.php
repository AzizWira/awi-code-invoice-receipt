<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id','action','subject_type','subject_id','summary','before_data','after_data'];
    protected function casts(): array { return ['before_data'=>'array','after_data'=>'array']; }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'customer.created' => 'Customer dibuat',
            'customer.updated' => 'Data customer diperbarui',
            'customer.archived' => 'Customer diarsipkan',
            'customer.restored' => 'Customer dipulihkan',
            'service_type.created' => 'Jenis jasa ditambahkan',
            'service_type.updated' => 'Jenis jasa diperbarui',
            'service_type.disabled' => 'Jenis jasa dinonaktifkan',
            'transaction.created' => 'Transaksi dibuat',
            'transaction.updated' => 'Transaksi diperbarui',
            'transaction.cancelled' => 'Transaksi dibatalkan',
            'payment.created' => 'Pembayaran ditambahkan',
            'payment.updated' => 'Pembayaran diperbarui',
            'payment.deleted' => 'Pembayaran dihapus',
            'document.generated' => 'Dokumen dibuat',
            'document.snapshot_regenerated' => 'Isi dokumen diperbarui',
            'document.token_regenerated' => 'Link publik diperbarui',
            'document.revoked' => 'Link publik dicabut',
            default => ucwords(str_replace(['.', '_'], ' ', (string) $this->action)),
        };
    }
}
