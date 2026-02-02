<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\TransactionInbound;
use App\Models\TransacationInboundItem;
use App\Models\Catalog;
use App\Models\CatalogSupplier;
use App\Models\TransactionOutbound;
use App\Models\TransactionOutboundItem;

class TransactionController extends Controller
{
    /**
     * Show the transaction management page.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'inbound');
        $search = $request->query('search');

        
        // Apply tab filter
        if ($tab === 'inbound') {
            $query = TransactionInbound::latest();
            
            // // Apply search filter
            // if ($search) {
            //     $query->where(function ($q) use ($search) {
            //         $q->where('name', 'like', "%{$search}%")
            //         ->orWhere('description', 'like', "%{$search}%");
            //     });
            // }
            
            $transactionInbounds = $query->paginate(12)->appends($request->query());
            
            // Counts for tabs (you can optimize this later with cached counts if needed)
            $counts = [
                'inbound'     => TransactionInbound::count(),
                'outbound' => TransactionOutbound::count(),
            ];
    
            return view('admin.transaction.index', compact('transactionInbounds', 'counts', 'search'));
        } else {
            $query = TransactionOutbound::latest();

            // // Apply search filter
            // if ($search) {
            //     $query->where(function ($q) use ($search) {
            //         $q->where('name', 'like', "%{$search}%")
            //         ->orWhere('description', 'like', "%{$search}%");
            //     });
            // }

            $transactionOutbounds = $query->paginate(12)->appends($request->query());

            // Counts for tabs (you can optimize this later with cached counts if needed)
            $counts = [
                'inbound'     => TransactionInbound::count(),
                'outbound' => TransactionOutbound::count(),
            ];
            return view('admin.transaction.index', compact('transactionOutbounds', 'counts', 'search'));
        }
    }
}