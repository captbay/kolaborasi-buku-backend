<?php

namespace App\Jobs;

use App\Models\transaksi_kolaborasi_buku;
use App\Models\transaksi_paket_penerbitan;
use App\Models\transaksi_penjualan_buku;
use App\Models\user_bab_buku_kolaborasi;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckIsDeadline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $id;
    public $type;

    /**
     * Create a new job instance.
     */
    public function __construct(String $id, String $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->type) {
            case 'buku':
                $data = transaksi_penjualan_buku::find($this->id);
                $data->update([
                    'date_time_exp' => null,
                    'status' => 'FAILED',
                ]);
                break;

            case 'kolaborasi':
                $data = transaksi_kolaborasi_buku::find($this->id);
                $data->update([
                    'date_time_exp' => null,
                    'status' => 'FAILED',
                ]);
                break;

            case 'paket':
                $data = transaksi_paket_penerbitan::find($this->id);
                if ($data->status == "TERIMA DRAFT") {
                    $data->update([
                        'status' => 'DP TIDAK SAH',
                        'date_time_exp' => null,
                    ]);
                } else if ($data->status == "DRAFT SELESAI") {
                    $data->update([
                        'status' => 'PELUNASAN TIDAK SAH',
                        'date_time_exp' => null,
                    ]);
                }
                break;

            case 'userbabkolaborasi':
                $data = user_bab_buku_kolaborasi::find($this->id);

                if ($data->datetime_deadline == Carbon::now()) {
                    $data->update([
                        'status' => 'FAILED',
                        'datetime_deadline' => null,
                    ]);
                }

                break;

            default:
                # code...
                break;
        }
    }
}
