<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;

    class SupportController extends Controller {
    public function storeRefund(Request $r) {
        $data = $r->validate([
        'reference'=>'required|string',
        'amount'=>'required|integer|min:1000',
        'reason'=>'nullable|string',
        ]);
        SupportTicket::create([
        'user_id'=>Auth::id(),'type'=>'refund','reference'=>$data['reference'],
        'payload'=>['amount'=>$data['amount'],'reason'=>$data['reason'] ?? null],
        'status'=>'open'
        ]);
        return back()->with('success','Pengajuan refund terkirim ke CS.');
    }

    public function storeSwap(Request $r) {
        $data = $r->validate([
        'reference'=>'required|string',
        'old_sp_id'=>'required|integer',
        'new_sp_id'=>'required|integer',
        'reason'=>'nullable|string',
        ]);
        SupportTicket::create([
        'user_id'=>Auth::id(),'type'=>'swap','reference'=>$data['reference'],
        'payload'=>['old_sp_id'=>$data['old_sp_id'],'new_sp_id'=>$data['new_sp_id'],'reason'=>$data['reason'] ?? null],
        'status'=>'open'
        ]);
        return back()->with('success','Pengajuan pindah kursi terkirim ke CS.');
    }
}
