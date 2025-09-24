<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * Get products that are low in stock.
     */
    public function lowStock(Request $request)
    {
        // Allow the client to specify a threshold, otherwise default to 10
        $threshold = $request->input('threshold', 10);

        $lowStockProducts = Product::where('stock_quantity', '<=', $threshold)
                                   ->orderBy('stock_quantity', 'asc')
                                   ->get();

        return response()->json($lowStockProducts);
    }

    /**
     * Get a summary of sales for a given period.
     */
    public function salesSummary(Request $request)
    {
        // Validate the period, allowing only specific values
        $request->validate([
            'period' => 'sometimes|in:today,week,month',
        ]);

        $period = $request->input('period', 'today'); // Default to 'today'

        $query = Order::query();

        if ($period === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }

        // Calculate the summary metrics
        $summary = [
            'total_revenue' => $query->sum('total_amount'),
            'transaction_count' => $query->count(),
        ];

        return response()->json($summary);
    }
}