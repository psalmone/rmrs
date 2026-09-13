<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of sales and invoices.
     */
    public function index(Request $request)
    {
        $status = $request->input('payment_status');
        $search = $request->input('search');

        $query = Sale::with(['customer', 'cashier', 'items'])->latest('sale_date');

        if ($status && in_array($status, ['paid', 'partial', 'unpaid'])) {
            $query->where('payment_status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $sales = $query->paginate(15)->withQueryString();

        $stats = [
            'total_sales' => Sale::count(),
            'total_revenue' => Sale::sum('net_amount'),
            'total_collected' => Sale::sum('paid_amount'),
            'outstanding' => Sale::sum('balance_amount'),
        ];

        return view('sales.index', compact('sales', 'stats', 'status', 'search'));
    }

    /**
     * Show the POS / Create Sale form.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $items = Item::where('is_active', true)
                     ->whereIn('category', ['milled_rice', 'by_product'])
                     ->orderBy('category')
                     ->orderBy('name')
                     ->get();

        $count = Sale::whereYear('created_at', now()->year)->count() + 1;
        $suggestedInvoice = 'INV-' . now()->year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('sales.create', compact('customers', 'items', 'suggestedInvoice'));
    }

    /**
     * Store a new sale, line items, and payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:sales,invoice_number',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id|nullable|string|max:255',
            'sale_type' => 'required|in:wholesale,retail',
            'discount_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,gcash,bank_transfer,credit',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if (!empty($validated['customer_id'])) {
            $customer = Customer::find($validated['customer_id']);
            $validated['customer_name'] = $customer ? $customer->name : ($validated['customer_name'] ?? 'Walk-in Customer');
        } else {
            $validated['customer_name'] = $validated['customer_name'] ?: 'Walk-in Customer';
        }

        $sale = DB::transaction(function () use ($validated) {
            $grossTotal = 0;
            $itemsToInsert = [];

            foreach ($validated['items'] as $line) {
                $item = Item::find($line['item_id']);
                $qty = (float) $line['quantity'];
                $price = (float) $line['unit_price'];
                $subtotal = $qty * $price;
                $grossTotal += $subtotal;

                $itemsToInsert[] = [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                ];

                // Deduct stock
                $item->decrement('current_stock', $qty);
            }

            $discount = (float) ($validated['discount_amount'] ?? 0);
            $netAmount = max(0, $grossTotal - $discount);
            $paid = (float) $validated['paid_amount'];
            $balance = max(0, $netAmount - $paid);

            $paymentStatus = 'paid';
            if ($paid <= 0) {
                $paymentStatus = 'unpaid';
            } elseif ($balance > 0) {
                $paymentStatus = 'partial';
            }

            $sale = Sale::create([
                'invoice_number' => $validated['invoice_number'],
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'sale_type' => $validated['sale_type'],
                'total_amount' => $grossTotal,
                'discount_amount' => $discount,
                'net_amount' => $netAmount,
                'paid_amount' => $paid,
                'balance_amount' => $balance,
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'cashier_user_id' => Auth::id(),
                'sale_date' => $validated['sale_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsToInsert as $line) {
                $sale->items()->create($line);
            }

            // If an initial payment was received, log transaction to Payment ledger
            if ($paid > 0) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $sale->customer_id,
                    'reference_number' => 'INIT-' . $sale->invoice_number,
                    'amount' => $paid,
                    'payment_method' => $validated['payment_method'],
                    'payment_date' => $validated['sale_date'],
                    'received_by_user_id' => Auth::id(),
                    'notes' => 'Initial payment at checkout',
                ]);
            }

            // Update customer balance if applicable
            if ($sale->customer_id && $balance > 0) {
                Customer::where('id', $sale->customer_id)->increment('outstanding_balance', $balance);
            }

            return $sale;
        });

        return redirect()->route('sales.show', $sale)
            ->with('success', "Invoice #{$sale->invoice_number} recorded successfully.");
    }

    /**
     * Display the specified invoice and receipt.
     */
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'cashier', 'items.item', 'payments.receivedBy']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Void/Remove the specified sale.
     */
    public function destroy(Sale $sale)
    {
        $invoice = $sale->invoice_number;

        DB::transaction(function () use ($sale) {
            // Restore inventory stocks
            foreach ($sale->items as $itemLine) {
                if ($item = Item::find($itemLine->item_id)) {
                    $item->increment('current_stock', $itemLine->quantity);
                }
            }

            // Deduct customer outstanding balance if was recorded
            if ($sale->customer_id && $sale->balance_amount > 0) {
                Customer::where('id', $sale->customer_id)->decrement('outstanding_balance', $sale->balance_amount);
            }

            $sale->payments()->delete();
            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')
            ->with('success', "Sale invoice #{$invoice} voided and stocks restored successfully.");
    }
}
