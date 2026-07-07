<?php

namespace Tests\Feature;

use App\Apps\Procurement\Events\GoodsReceived;
use App\Apps\Procurement\Models\PurchaseOrder;
use App\Apps\Procurement\Models\PurchaseRequest;
use App\Apps\Procurement\Models\Supplier;
use App\Apps\Procurement\ProcurementService;
use App\Events\Domain\PurchaseOrderApproved;
use App\Events\Domain\SupplierCreated;
use App\Marketplace\AppManager;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/** PLATFORM-002 Part 9 — Procurement App foundation. */
class ProcurementAppTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->merchant = Merchant::factory()->create([
            'user_id'                 => $this->user->id,
            'onboarding_completed_at' => now(),
        ]);
        app(AppManager::class)->install($this->merchant, 'procurement');
        $this->user->refresh();
    }

    private function draftRequest(?int $supplierId = null): PurchaseRequest
    {
        return PurchaseRequest::create([
            'merchant_id'    => $this->merchant->id,
            'supplier_id'    => $supplierId,
            'title'          => 'Coffee beans restock',
            'items'          => [['name' => 'Beans 1kg', 'qty' => 10, 'est_cost' => 350]],
            'estimated_cost' => 3500,
            'requested_by'   => $this->user->id,
        ]);
    }

    public function test_routes_403_without_app(): void
    {
        app(AppManager::class)->uninstall($this->merchant->fresh(), 'procurement');

        $this->actingAs($this->user->fresh())
            ->get(route('procurement.index', absolute: false))
            ->assertForbidden();
    }

    public function test_supplier_crud_and_vendor_rating(): void
    {
        $this->actingAs($this->user)->post(route('procurement.suppliers.store'), [
            'name' => 'Bangkok Beans Co', 'category' => 'Coffee',
        ])->assertRedirect(route('procurement.index', absolute: false));

        $supplier = Supplier::firstWhere('name', 'Bangkok Beans Co');
        $this->assertSame($this->merchant->id, $supplier->merchant_id);

        $this->actingAs($this->user)->post(route('procurement.suppliers.rate', $supplier), ['stars' => 5]);
        $this->actingAs($this->user)->post(route('procurement.suppliers.rate', $supplier), ['stars' => 4]);

        $fresh = $supplier->fresh();
        $this->assertSame('4.50', (string) $fresh->rating_avg);
        $this->assertSame(2, $fresh->rating_count);
    }

    public function test_approval_workflow_creates_purchase_order_and_emits_event(): void
    {
        Event::fake([PurchaseOrderApproved::class]);
        $supplier = Supplier::create(['merchant_id' => $this->merchant->id, 'name' => 'Beans Co']);
        $pr = $this->draftRequest($supplier->id);

        // draft → approve directly is blocked
        $this->actingAs($this->user)->put(route('procurement.requests.approve', $pr))
            ->assertSessionHasErrors(['status']);

        $this->actingAs($this->user)->put(route('procurement.requests.submit', $pr));
        $this->actingAs($this->user)->put(route('procurement.requests.approve', $pr->fresh()));

        $pr->refresh();
        $this->assertSame('ordered', $pr->status);
        $this->assertNotNull($pr->approved_at);
        $this->assertSame($this->user->id, $pr->approved_by);

        $po = PurchaseOrder::first();
        $this->assertSame($supplier->id, $po->supplier_id);
        $this->assertSame('3500.00', (string) $po->total_cost);
        Event::assertDispatched(PurchaseOrderApproved::class, fn ($e) => $e->name() === 'purchase_order.approved');
    }

    public function test_rejection_records_reason(): void
    {
        $pr = $this->draftRequest();
        app(ProcurementService::class)->submit($pr);

        $this->actingAs($this->user)->put(route('procurement.requests.reject', $pr->fresh()), [
            'reason' => 'Too expensive this month',
        ]);

        $fresh = $pr->fresh();
        $this->assertSame('rejected', $fresh->status);
        $this->assertSame('Too expensive this month', $fresh->rejection_reason);
    }

    public function test_receiving_goods_fires_inventory_hook(): void
    {
        Event::fake([GoodsReceived::class]);
        $supplier = Supplier::create(['merchant_id' => $this->merchant->id, 'name' => 'Beans Co']);
        $po = PurchaseOrder::create([
            'merchant_id' => $this->merchant->id, 'supplier_id' => $supplier->id,
            'items' => [['name' => 'Beans 1kg', 'qty' => 10]], 'total_cost' => 3500,
        ]);

        $this->actingAs($this->user)->put(route('procurement.orders.receive', $po));

        $this->assertSame('received', $po->fresh()->status);
        Event::assertDispatched(GoodsReceived::class, fn ($e) => $e->name() === 'goods.received');
    }

    public function test_supplier_creation_emits_domain_event(): void
    {
        Event::fake([SupplierCreated::class]);

        Supplier::create(['merchant_id' => $this->merchant->id, 'name' => 'Event Foods']);

        Event::assertDispatched(SupplierCreated::class, fn ($e) => $e->name() === 'supplier.created');
    }

    public function test_procurement_is_tenant_scoped(): void
    {
        $otherOwner = User::factory()->create(['email_verified_at' => now()]);
        $other = Merchant::factory()->create(['user_id' => $otherOwner->id]);
        $foreignPr = PurchaseRequest::create([
            'merchant_id' => $other->id, 'title' => 'Foreign', 'items' => [], 'status' => 'submitted',
        ]);

        $this->actingAs($this->user)
            ->put(route('procurement.requests.approve', $foreignPr))
            ->assertForbidden();
    }
}
