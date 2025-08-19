<?php

namespace App\Http\Controllers;

use App\Http\Resources\MutationsDetailResource;
use App\Http\Resources\MutationsResource;
use App\Models\Akun;
use App\Models\Mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $data['akun'] = Akun::all();
        return view('transactions/transfer', $data);
    }

    public function transfer(Request $request)
    {
        $idPengirim = $request->id_pengirim;
        $idPenerima = $request->id_penerima;
        $saldoTf = $request->jumlah_saldo;

        $querySaldoAwalPengirim = DB::table('akuns')->select('saldo')->where('id', $idPengirim)->first();
        $saldoAwalPengirim = $querySaldoAwalPengirim->saldo;
        $saldoAkhirPengirim = $saldoAwalPengirim - $saldoTf;

        $querySaldoAwalPenerima = DB::table('akuns')->select('saldo')->where('id', $idPenerima)->first();;
        $saldoAwalPenerima = $querySaldoAwalPenerima->saldo;
        $saldoAkhirPenerima = $saldoAwalPenerima + $saldoTf;

        DB::beginTransaction();
        try {

            $this->pengirim($idPengirim, $saldoAkhirPengirim);
            $this->penerima($idPenerima, $saldoAkhirPenerima);
            $mutations = Mutation::create([
                'jumlah_saldo_tf' => $saldoTf,
                'id_pengirim' => $idPengirim,
                'saldo_awal_pengirim' => $saldoAwalPengirim,
                'saldo_akhir_pengirim' => $saldoAkhirPengirim,
                'id_penerima' => $idPenerima,
                'saldo_awal_penerima' => $saldoAwalPenerima,
                'saldo_akhir_penerima' => $saldoAkhirPenerima,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
        }
    }

    private function pengirim($id, $saldo)
    {
        $pengirim = Akun::findOrFail($id);
        $pengirim->update([
            'saldo' => $saldo
        ]);
    }

    private function penerima($id, $saldo)
    {
        $pengirim = Akun::findOrFail($id);
        $pengirim->update([
            'saldo' => $saldo
        ]);
    }
    private function mutasi($saldoTf, $idPengirim, $saldoAwalPengirim, $saldoAkhirPengirim, $idPenerima, $saldoAwalPenerima, $saldoAkhirPenerima)
    {
        $mutations = Mutation::create([
            'jumlah_saldo_tf' => $saldoTf,
            'id_pengirim' => $idPengirim,
            'saldo_awal_pengirim' => $saldoAwalPengirim,
            'saldo_akhir_pengirim' => $saldoAkhirPengirim,
            'id_penerima' => $idPenerima,
            'saldo_awal_penerima' => $saldoAwalPenerima,
            'saldo_akhir_penerima' => $saldoAkhirPenerima
        ]);
        // var_dump($mutations);
    }
}
