1. Update Supplier model
2. Update Supplier Controller
3. Update supplier js script
4. Added a new menu for supplier under the Service menu
5. Like the Category and Brand we need to design the UI
6. We need to show data in the data table but pagination not from backend or dynamic
7. In the data table we need to add a column products and need to show all the products under this supplier
8. No need to show the created by created at or updated and by and time, not need to show the multiple phone number
9. Do not show the details in the model, we need a dedicated page for supplier show
10. Like the service and customer we need to add the top ribbon for supplier
11. In the details page we need a table to show the product details that are from this supplier


1. Add a new menu for product under the service, and reanamed the service menu to Product & Service
2. In the index page show all the products
- show all the product in data table with show, edit, delete button
- Keep a add new button in the top with back button like service page - (check service index page)
- Show total number of variation in the data table
- Code, Name, Unit, Brand, Supplier, total variation and action buttons
3. edit, show need seperate page
4. Add new product
- We need a modal to add new product, no need separate page
- No need to keep price here as price is for service
- after create the product we need to redirect to the product edit page
5. Product Edit
- Top section will show the product info in the edit field
- bottom section will show a product variation form like table
- Table top will contain a button to add new variant
- So from this page we willa able to add new variation
- New variation will show in the table after adding
- As the table is actually a variation edit form, so user will able to update the variation information.
- We need to add a button to delete the variation

- Future----------------------------
- Product delete 
- variation delete can't deleted
- can't updated the price


- We need to remove the supplier from the product and need to create relation with pruchases
- Purchases Module Design
-- User will visit the purchaes module to entry the purchases
-- User will select the product, if the product not available user will create the product
-- User will select and provide the basic info about the purchases


but here is another issue
I am planning to not giving the update the price of a variant if the variant has any sales
so if a variant has stock 10 remaining and i purchaes 100 more and the price is also need to increases
what can i do
I will move the remaining 10 to new variant with update price and with same value and exising will be active false.
and this new variant will be now 10+100 stock

Purchases Module
- This will contain purchase and purchases item table.
- Purchases will keep all basic info, total cost price and supplier id info and product id
- Purchases Item will keep product variant id and quantity cost price per variant 
- Now we need to update the produt variant's stock that right?

- Now what is my actula stock? I am thinking purchases quantity is my stock, but what should be the name of product variant's stock? because i will show this value in the ecommerce and i will sell and reduce this value this is my actual selling stocks
- Now if we consider purchaes item stock is the main stock we need to reduce this stock but I also need to keep track how many item i purchases.
- Should we keep a column like buying quantity and ecommerce quantity 
- Basiclly there may be a scenario like I do not want to sell all the items now, i want to keep some product outside of the ecommerce stock. how can we do that.




- Purchase Module
- Select Product -> Show Product Name and All varient in a table
-> Table will show Variant Tag Line, Short Description, Unit Cost Price, Items QTy
-> This data will be saved in the purchases item table
-> Tag line will be come from product variant table
-> Purchase Item will store new entry every time, It will have a hidden column called available qty, this will not how at the time of add
-> At the time of adding the item qty and availabel qty will be same.

- Product Module
-> Product Eidt Page
-> A action button will be shown in the variant table called add stock
-> By clicking the button a modal(Add stock modal) will show and the modal will contain all the purchases item of this product and this tag line having vailable qty > 0
-> The modal will show the purchases date, tag line, cost price, and vailble qty and a editable input box to give user input to stock the selling stock for ecommerce
-> after submiting the process available item of the pruchases item will be reduce.
-> there will be another button called reduce stock
-> by clicking the button a modal will shows
-> User will select Reduce reason like (damange, creating dedicated product, creating new variante, creating new variane to update price) and a note box
-> So this way user will able to reduce stock if need to do without sell, of all item reduced we will mark this avtive as fale.
-> User Info: You can't update the price of this variant as this as already some sales. So, you can reduce the stock and mark as active false and create new varient with update price
-> so variable table's tag line is not unique, But unique within the active variants.
-> for example Tag line S-Size could have multiple variane but active must be only one.
-> So, we can keep store like S-Size 300 tk - active false
-> S-Size 330 tk active true
-> Another point use can't create same tag line variable having stock > 0 both.
- For example S-Size 300 tk - false - stock = 0
- S-Size - 330 tk true - stock = 25

- So finally i have
-> Availble QTY in Stock (purchase item available qty), Stock in warehouse
-> Availble QTY in ecommerce (product variants qty) Stock in shop / ecommerce



1. Update Supplier model
2. Update Supplier Controller
3. Update supplier js script
4. Added a new menu for supplier under the Service menu
5. Like the Category and Brand we need to design the UI
6. We need to show data in the data table but pagination not from backend or dynamic
7. In the data table we need to add a column products and need to show all the products under this supplier
8. No need to show the created by created at or updated and by and time, not need to show the multiple phone number
9. Do not show the details in the model, we need a dedicated page for supplier show
10. Like the service and customer we need to add the top ribbon for supplier
11. In the details page we need a table to show the product details that are from this supplier


1. Add a new menu for product under the service, and reanamed the service menu to Product & Service
2. In the index page show all the products
- show all the product in data table with show, edit, delete button
- Keep a add new button in the top with back button like service page - (check service index page)
- Show total number of variation in the data table
- Code, Name, Unit, Brand, Supplier, total variation and action buttons
3. edit, show need seperate page
4. Add new product
- We need a modal to add new product, no need separate page
- No need to keep price here as price is for service
- after create the product we need to redirect to the product edit page
5. Product Edit
- Top section will show the product info in the edit field
- bottom section will show a product variation form like table
- Table top will contain a button to add new variant
- So from this page we willa able to add new variation
- New variation will show in the table after adding
- As the table is actually a variation edit form, so user will able to update the variation information.
- We need to add a button to delete the variation

- Future
- Product delete 
- variation delete can't deleted
- can't updated the price