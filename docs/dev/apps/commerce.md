# Commerce App (APP-001/002/003, BETA-008A)

Key: `commerce`. Predates the SDK; registered in the marketplace with a full
manifest (no provider class yet — routes live in routes/web.php gated by
`app.installed:commerce`). Migrating it onto an AppProvider is a cheap,
optional refactor.

- Catalogue: products (one main image, merchant-scoped storage), free-typed
  categories, optional stock tracking.
- Storefront: public per-merchant page (`/store/{slug}`), customer-language
  aware (BETA-008B), ordering with pickup/delivery/shipping fulfillment.
- Orders: status machine (`Order::TRANSITIONS`), merchant-confirmed payment
  marker only — payment is always direct customer → merchant (ADR-011).
- Emits `order.placed` and `payment.received` on the event bus.
