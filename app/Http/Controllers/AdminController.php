<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\TransactionInbound;
use App\Models\TransactionOutbound;
use App\Models\OtherCost;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function showDashboard(Request $request)
    {
        $month = $request->input('month', now()->format('m')); // '01'–'12'
        $year  = $request->input('year', now()->year);

        // ========================================
        // Inbound Transactions (Purchases)
        // ========================================
        $inboundQuery = TransactionInbound::query()
            ->when($month, fn($q) => $q->whereMonth('created_date', $month))
            ->when($year,  fn($q) => $q->whereYear('created_date', $year));

        $inboundPaidQuery    = (clone $inboundQuery)->where('is_paid', true);
        $inboundUnpaidQuery  = (clone $inboundQuery)->where('is_paid', false);

        $purchasePricePaid   = $inboundPaidQuery->sum('total_price');
        $purchasePriceUnpaid = $inboundUnpaidQuery->sum('total_price');
        $inboundCount        = $inboundQuery->count();

        $inbounds = $inboundQuery->latest('created_date')
            ->paginate(10)
            ->appends($request->query());

        // ========================================
        // Outbound Transactions (Sales)
        // ========================================
        $outboundQuery = TransactionOutbound::query()
            ->when($month, fn($q) => $q->whereMonth('created_date', $month))
            ->when($year,  fn($q) => $q->whereYear('created_date', $year));

        $outboundPaidQuery   = (clone $outboundQuery)->where('is_paid', true);
        $outboundUnpaidQuery = (clone $outboundQuery)->where('is_paid', false);

        $outboundTotalAll    = $outboundQuery->sum('total_price');
        $netProfitPaid       = $outboundPaidQuery->sum('net_profit');
        $netProfitUnpaid     = $outboundUnpaidQuery->sum('net_profit');
        $outboundCount       = $outboundQuery->count();

        $outbounds = $outboundQuery->latest('created_date')
            ->paginate(10)
            ->appends($request->query());

        // ========================================
        // Other Costs (In & Out)
        // ========================================
        $otherCostQuery = OtherCost::query()
            ->when($month, fn($q) => $q->whereMonth('date', $month))
            ->when($year,  fn($q) => $q->whereYear('date', $year));

        $otherCostIn  = $otherCostQuery->clone()->where('type', 'in')->sum('price');
        $otherCostOut = $otherCostQuery->clone()->where('type', 'out')->sum('price');

        // ========================================
        // Final Calculations
        // ========================================
        $netProfit = $netProfitPaid - $otherCostOut + $otherCostIn;     // Final Net Profit (realized)
        $omset     = $outboundTotalAll + $otherCostIn;                  // Omset = All sales + other income

        $totalUsers = User::count();

        // ========================================
        // Return to View
        // ========================================
        return view('admin.dashboard', compact(
            'totalUsers',
            'netProfit',           // Final realized profit
            'netProfitPaid',       // Only from paid sales (before other costs)
            'netProfitUnpaid',     // Potential profit from unpaid sales
            'purchasePricePaid',
            'purchasePriceUnpaid',
            'omset',
            'otherCostIn',
            'otherCostOut',
            'inboundCount',
            'outboundCount',
            'inbounds',
            'outbounds'
        ));
    }

    /**
     * Show the user management page.
     */
    public function index()
    {
        $users = User::query()
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.user.index', compact('users'));
    }

    /**
     * Verify a user.
     */
    public function _verifyUser(User $user)
    {
        // Verify the user
        $user->is_verified = true;
        $user->save();

        return redirect()->route('admin.user.index')->with('success', 'User verified successfully.');
    }

    /**
     * Delete a user.
     */
    public function _deleteUser(User $user)
    {
        // Delete the user
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'User deleted successfully.');
    }
}