<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseFormatter;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit',6);
        $status = $request->input('status');

        if ($id)
        {
            $transaaction = Transaction::with(['item.product'])->find($id);

            if ($transaaction)
            {
                return ResponseFormatter::success(
                    $transaaction,
                    'Data transaction berhasil diambil'
                );
            }
        }
        else
        {
            return ResponseFormatter::error(
                null,
                'Data transaction tidak ada',
                484
            );
        }

        $transaaction = Transaction::with(['item.product'])->where('user_id', Auth::user()->id);

        if($status)
        {
            $transaaction->where('status', $status);
        }

        return ResponseFormatter::success(
            $transaaction->paginate($limit),
            'Data list berhasil diambil'
        );
    }

    public function chackout(Request $request)
    {
        $request->validate([
            'item'=> 'required|array',
            'item.*id'=> 'exist:products,id',
            'total_price'=> 'required',
            'status' => 'required|in:PENDIGN, SUCCESS, CANCLLED, FAILED, SHIPPING, SHIPPED'
        ]);

        $transaction = Transaction::create([
            'users_id'=> Auth::user()->id,
            'address'=> $request->address,
            'total_price'=> $request->total_price,
            'shipping_price'=> $request->shipping_price,
            'status'=> $request->status,
        ]);

        foreach ($request->items as $product)
        {
            TransactionItem::create([
                'users_id'=> Auth::user()->id,
                'product_id'=> $product['id'],
                'transaction'=> $transaction->id,
                'quantity' => $product['quantity']
            ]);
        }

        return ResponseFormatter::success($transaction->load('item.product'), 'Transaksi behasil');
    }
}