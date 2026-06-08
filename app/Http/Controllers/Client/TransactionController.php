<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Transaction;

class TransactionController extends Controller
{
 public function transactions()
{
    $transactions = auth()->user()->transactions;
    return view('client.transactions', compact('transactions'));
}

}


