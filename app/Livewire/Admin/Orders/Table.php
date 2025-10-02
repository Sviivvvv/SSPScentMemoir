<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;

class Table extends Component
{
    use WithPagination;

    /** date chip: all | 7d | 30d */
    public string $period = 'all';

    /** search by: order id, customer name, email */
    public string $q = '';

    /** unique page param */
    public string $pageName = 'orders_page';

    /** modal state */
    public bool $show = false;
    public ?int $orderId = null;

    /** keep filters/search in the URL */
    protected $queryString = [
        'period' => ['except' => 'all'],
        'q' => ['except' => ''],
    ];

    public function mount(string $pageName = 'orders_page'): void
    {
        $this->pageName = $pageName;
    }

    public function updatingPeriod(): void
    {
        $this->resetPage($this->pageName);
    }

    public function updatingQ(): void
    {
        $this->resetPage($this->pageName);
    }

    public function setPeriod(string $period): void
    {
        $allowed = ['all', '7d', '30d'];
        $this->period = in_array($period, $allowed, true) ? $period : 'all';
        $this->resetPage($this->pageName);
    }

    public function clearSearch(): void
    {
        $this->q = '';
        $this->resetPage($this->pageName);
    }

    protected function baseQuery()
    {
        $q = Order::query()
            ->with(['user:id,name,email', 'items.product:id,name,image_path,price'])
            ->latest('ordered_at');

        // date filter
        if ($this->period === '7d') {
            $q->where('ordered_at', '>=', Carbon::now()->subDays(7));
        } elseif ($this->period === '30d') {
            $q->where('ordered_at', '>=', Carbon::now()->subDays(30));
        }

        // search
        $termRaw = trim($this->q);
        if ($termRaw !== '') {
            $term = '%' . str_replace(['%', '_'], ['\%', '\_'], $termRaw) . '%';
            $q->where(function ($w) use ($term, $termRaw) {
                if (ctype_digit($termRaw)) {
                    $w->orWhere('id', (int) $termRaw);
                }
                $w->orWhereHas('user', function ($u) use ($term) {
                    $u->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            });
        }

        return $q;
    }

    /** modal controls */
    public function open(int $orderId): void
    {
        $this->orderId = $orderId;
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->orderId = null;
    }

    public function render()
    {
        $orders = $this->baseQuery()->paginate(10, ['*'], $this->pageName);
        $order = $this->orderId
            ? Order::with(['user', 'items.product'])->find($this->orderId)
            : null;

        return view('livewire.admin.orders.table', compact('orders', 'order'));
    }
}
