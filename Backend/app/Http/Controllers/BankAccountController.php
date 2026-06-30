<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        return response()->json(BankAccount::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:50',
        ]);

        $account = BankAccount::create([
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return response()->json($account, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:50',
        ]);

        $account = BankAccount::find($id);

        if (!$account) {
            return response()->json(['message' => 'Rekening tidak ditemukan.'], 404);
        }

        $account->update([
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return response()->json($account);
    }

    public function destroy($id)
    {
        $account = BankAccount::find($id);

        if (!$account) {
            return response()->json(['message' => 'Rekening tidak ditemukan.'], 404);
        }

        $account->delete();

        return response()->json(['message' => 'Rekening berhasil dihapus.']);
    }
}
