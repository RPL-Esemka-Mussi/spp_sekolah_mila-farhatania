<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Spp;
use Illuminate\Http\Request;
// use PDF;

class pembayaranController extends Controller
{
    public function index(Request $request)
    {
        $keyword = null;

        if($request->cari != null){
            $siswa = Siswa::with('user')->whereRelation('user', 'name','like', "%$request->cari%")->orderBy('kelas_id', 'ASC')->get();
            $keyword =$request->cari;

        }else{
            if(auth()->user()->level == 'siswa'){
                $siswa = Siswa::where('user_id','=', auth()->user()->id)->get();
            }else{
                $siswa = Siswa::orderBy('kelas_id', 'ASC')->get();
            }
        }

        return view('pembayaran.index', compact('siswa', 'keyword'));
    }

    public function transaksi($id)
    {
        $dibayar = 0;
        $tagihan = 0;
        $pembayaranSpp = [];

       try{
        $siswa = Siswa::findOrFail($id);
        $pembayaran = Pembayaran::where('siswa_id' ,$id)->get();
        $spp = Spp::get();

        foreach ($pembayaran as $data)
        {
            $dibayar += $data->jumlah_bayar;
        }

        foreach ($spp as $data)
        {
            $tagihan += $data->nominal;

           $total = Pembayaran::where('spp_id', $data->id)->where('siswa_id', $id)->sum('jumlah_bayar');
           $pembayaranSpp[] = $total;
        }

        $total = [
            'total_dibayar' => $dibayar,
            'total_belumdibayar' => $tagihan - $dibayar
        ];

       }catch(\Exception $e){
        return redirect('pembayaran')->with('gagal', 'pembayaran gagal');
       }

       return view('pembayaran.transaksi', compact('spp', 'pembayaran', 'siswa', 'total', 'pembayaranSpp'));
    }

    public function simpan(Request $request)
    {

        try {
          Pembayaran::create([
                'user_id' => auth()->user()->id,
                'siswa_id' => $request->siswa_id,
                'spp_id' => $request->spp_id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar' => $request->jumlah_bayar,

            ]);


            return redirect()->back()->with('sukses', "Transaksi Berhasil masuk ✨." );


        }catch(\Exception $e)
        {

            return redirect()->back()->with('gagal', "Transaksi tidak masuk 🧨." .$e->getMessage() );
        }
    }
    

    public function edit($id)
    {
        try{
            $data = Pembayaran::findOrFail($id);
            $spp = Spp::all();
            $siswa = Siswa::all();
            $user = User::all();


            return view('pembayaran.edit', compact('data', 'spp', 'siswa', 'user'));

            return redirect('pembayaran')->with('sukses' ,'data berhasil di edit✨.');
        }catch(\Exception $e){
            return redirect('pembayaran')->with('gagal' ,'data tidak di edit🧨.');
        }
    }

    public function update(Request $request)
    {
        

        try{
            Pembayaran::where('id', $request->id)->update([
                'jumlah_bayar' => $request->jumlah_bayar,
            ]);

            return redirect('pembayaran')->with('sukses' ,'data berhasil di update✨.');
        }catch(\Exception $e){
            return redirect('pembayaran')->with('gagal' ,'data tidak berhasil di update🧨.');
        }

    }

    public function hapus($id)
    {
        try{
            Pembayaran::findOrFail($id);
            Pembayaran::destroy($id);

            return redirect()->back()->with('sukses' ,'data berhasil di hapus ✨.');
        }catch(\Exception $e){
            return redirect()->back()->with('gagal' ,'data tidak bisa di hapus 🧨.');
        }
    }
}


