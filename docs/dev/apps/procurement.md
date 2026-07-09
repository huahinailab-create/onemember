# Procurement App (PLATFORM-002 P9)

Key: `procurement`.

- **Entities**: `suppliers` (category, contacts, incremental 1–5 vendor
  rating), `purchase_requests` (items JSON, estimated cost, approval
  workflow), `purchase_orders` (actual cost tracking), `goods_receipts`.
- **Workflow** (`ProcurementService`): PR `draft → submitted →
  approved | rejected`; approval records approver + timestamp, raises the PO
  (`purchase_order.approved` event) and moves the PR to `ordered`.
  Single-step approval today; multi-level chains are the documented
  extension point. Rejection requires a reason.
- **Inventory hooks**: receiving goods (`receive`) records a receipt, marks
  the PO `received`, and emits `goods.received` — the future Inventory App
  subscribes to adjust stock. `supplier.created` emits from the model.
- **Money**: costs here are the merchant's own purchasing records —
  OneMember still never processes payments (ADR-011).
