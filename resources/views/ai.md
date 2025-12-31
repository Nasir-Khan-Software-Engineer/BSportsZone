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