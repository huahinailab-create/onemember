# Domain Event Bus (PLATFORM-002 P3)

Modules communicate by events, not direct calls. All events extend
`App\Events\Domain\DomainEvent` and are emitted from **model lifecycle
hooks** (`DomainEventServiceProvider`) so every code path — UI, CSV import,
counter mode, identity flows, API — behaves identically.

## Shipped events (names are stable; never rename)
| Name | Class | Fired when |
|---|---|---|
| `member.created` | MemberCreated | Member row created (any path) |
| `purchase.recorded` | PurchaseRecorded | Earn transaction hits the ledger |
| `reward.redeemed` | RewardRedeemed | Redemption created |
| `merchant.registered` | MerchantRegistered | Merchant tenant created |
| `order.placed` | OrderPlaced | Storefront order created |
| `payment.received` | PaymentReceived | Merchant marks an order paid (self-reported — ADR-011: OneMember never touches money) |
| `subscription.changed` | SubscriptionChanged | Subscription status/plan changes |
| `queue.ticket_created` | QueueTicketCreated | Queue App issues a ticket |
| `supplier.created` | SupplierCreated | Procurement supplier created |
| `purchase_order.approved` | PurchaseOrderApproved | PR approval raises a PO |
| `goods.received` | Apps\Procurement GoodsReceived | Goods receipt recorded (Inventory hook) |

## Rules
- `payload()` must stay custodian-safe: ids + merchant-owned business fields
  only; no cross-merchant data, no secrets, no full PII dumps.
- Events dispatch synchronously — slow consumers queue themselves.
- Subscribe with `Event::listen('App\\Events\\Domain\\*', Handler::class)`
  (handler signature: `handle(string $eventName, array $data)`), or listen to
  a concrete class. Webhooks (P4) and Automation (P6) use the wildcard.
- Adding an event: extend DomainEvent, hook the emitting model/service, add
  a row here, cover with a test in DomainEventBusTest.
