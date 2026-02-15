<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\OtherCost;

class OtherCostController extends Controller
{
    /**
     * Show the other cost management page.
     */
    public function index(Request $request)
    {
        $tab    = $request->query('tab', 'in');
        $search = $request->query('search');
        $month  = $request->query('month');   // now expected as '01', '02', ..., '12' or empty
        $year   = $request->query('year');    // e.g. '2024', '2025' or empty

        $query = OtherCost::query()->latest();

        // Tab filter: 'in' → income, 'out' → expense/cost
        $query->where('type', $tab === 'out' ? 'out' : 'in');

        // Search by name
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Month filter (handle '01' → 1, etc.)
        if ($month && in_array($month, array_map(fn($m) => sprintf('%02d', $m), range(1,12)))) {
            $query->whereMonth('created_at', (int) ltrim($month, '0'));
        }

        // Year filter
        $currentYear = now()->year;
        if ($year && is_numeric($year) && $year >= ($currentYear - 10) && $year <= ($currentYear + 5)) {
            $query->whereYear('created_at', $year);
        }

        // Pagination (keeps all query parameters)
        $costs = $query->paginate(20)->appends($request->query());

        // Counts for tabs (total per type - no is_active since type is the discriminator)
        $counts = [
            'in'  => OtherCost::where('type', 'in')->count(),
            'out' => OtherCost::where('type', 'out')->count(),
        ];

        return view('admin.other_cost.index', compact('costs', 'counts', 'search', 'tab', 'month', 'year'));
    }

    /**
     * Store a newly created other cost for the given supplier.
     */
    public function _createNewOtherCost(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'type'  => 'required|in:in,out',
            'date'  => 'required|date',
        ]);

        $other_cost = OtherCost::create($validated);

        return redirect()
            ->route('admin.other-cost.index')
            ->with('success', "Cost \"{$other_cost->name}\" has been successfully added.");
    }

    /**
     * Delete other cost.
     */
    public function _deleteOtherCost(OtherCost $other_cost)
    {
        // Check the date if the date >= now(), show warning message
        if (now()->greaterThan($other_cost->date)) {
            return redirect()
            ->route('admin.other-cost.index')
            ->with('fail', "Other cost \"{$other_cost->name}\" cannot be deleted because date already passed.");
        }
        
        // Delete other cost
        $other_cost->delete();

        // Redirect with success message
        return redirect()
            ->route('admin.other-cost.index')
            ->with('success', "Cost \"{$other_cost->name}\" has been successfully deleted.");
    }
}