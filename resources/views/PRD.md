📘 PRD — Purchases Module (Inventory Entry)
1️⃣ Menu & Navigation
Menu Structure
Sales
Stock
 └─ Purchases


Purchases module lives under Stock

Reason: Purchases are inventory entry, not sales or accounting

2️⃣ Purpose of Purchases Module

The Purchases module is responsible for:

Recording incoming stock from suppliers

Creating warehouse stock (unallocated stock)

Providing the only entry point for increasing inventory

Supporting future allocation to product variants

❗ Purchases do NOT affect ecommerce sellable stock directly

3️⃣ Purchases Database Design
🧱 purchases (Header Table)
purchases
- id
- pos_id                  -- optional, for POS / branch reference
- purchase_date
- invoice_number
- name                    -- purchase title / reference name
- supplier_id
- product_id              -- ⚠️ see note below
- total_qty
- total_cost_price
- description             -- long text
- status                  -- draft / confirmed
- created_by
- created_at
- updated_at


🧱 purchase_items (Warehouse Stock)
purchase_items
- id
- purchase_id
- product_variant_id
- cost_price
- purchased_qty           -- fixed, never changes
- unallocated_qty         -- warehouse stock
- created_at


📌 Rule:

purchased_qty == unallocated_qty (at creation)

4️⃣ Index Page (Purchases List)
Page Behavior

Backend paginated (like products & services)

Searchable

Sortable by date / invoice

Columns
Column
Purchase Date
Invoice Number
Purchase Name
Product Name
Total Cost
Total Quantity
Supplier
Actions (View / Edit)
5️⃣ Add Purchase Page
Layout
🔹 Top Section — Purchase Info Form

Fields:

Purchase Date

Invoice Number

Purchase Name

Supplier (select)

Product (select)

Description (long text)

🔹 Bottom Section — Variant Stock Entry Table

When product is selected, load all active variants.

Tag Line	Cost Price	New Stock Qty	Sellable Stock	Action
S Size	[input]	[input]	auto-filled	Add
M Size	[input]	[input]	auto-filled	Add
Logic

Sellable Stock = New Stock (read-only)

Clicking Add:

creates a purchase_items row

sets:

purchased_qty = input qty
unallocated_qty = input qty


Totals auto-calculated:

total_qty

total_cost_price

6️⃣ Edit Purchase Page
Rules

Purchase header info is editable

Purchase items are editable row

Editable Conditions (VERY IMPORTANT)

A purchase item row is editable only if:

purchased_qty == unallocated_qty


Meaning:

No stock has been allocated yet

Safe to modify

Editable Fields (Per Row)

Tagline (optional ⚠️)

Cost Price

Purchased Qty

❌ Editing disabled if:

Any allocation exists

unallocated_qty < purchased_qty

7️⃣ Delete Purchase (Deferred)

❌ Not implemented now

Reason: deletion can break stock integrity

Future approach:

soft delete

only allowed if no allocation exists

8️⃣ Validation Rules
Backend Validation

purchased_qty > 0

cost_price ≥ 0

same variant cannot be added twice in one purchase

total_qty = sum(purchase_items.qty)

Note: FOllow product module design for UI Design
DO not add any permission now