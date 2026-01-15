- Now at the time of loading the dashboard we can calcualte the following metrics
- Todays customer - customer created at today
- Todays Order - order created at today
- Todays Inprogress - order status is inprogress and -> order has many lifecycle in progress status created at today
- Completed Order: Same Status Completed and -> status created at today
- Return Order: Same return status created at today
- Todats Sold Amount: Order Completed today and total amount

Order Metrics
- Total Order all status
- Total Order Pending Order
- Toal InProgress Order
- Total Cancelled Order
- Completed Order
- Return Order

Products
- Total Products
- Unpublished Products
- Low Sotck Products: not now, we will calculate via ajax
- Sellable Stock: not now, we will calculate via ajax
- Warehouse Stock:  not now, we will calculate via ajax
- Return Qty:  not now, we will calculate via ajax

Expense
- Total Expsens: Calculate total
- Total Purchases Cost: not now, we will calculate via ajax
- Total Ad Cost: not now, we will calculate via ajax
- Total Delivery Cost: not now, we will calculate via ajax
- Total Sold Qty: Completed order qty
- Total Sold Amount: Completed order padi amount

The not now metrics will be calcualted via ajax after page load.
so keep those now.
Calcualte from backend controller and pass the data to view


------ Order Lifecycle 
1. We need to crete a model amd migration
- ID, POSID, Status, Note, Created at, Updated at, Created By, Updated By
2. We need to add a button in the order show page at the top.
3. The button will be dynamic like, Mark as Confirmed, Mark as Cancelled, ....
4. There is the following status and a order shoudl must follow the order or the status.
- Pendding, Confirmed or Cancelled, Delivered to Courier
5. After clicking the button a modal will open to input the optional note.
6. After pending we need to show 2 buttons Mark as confirmed and mark as cancelled
- after cancelled there is no button needed
- After confirmed we need to show the delivered button
- After Delivered we need to show Received Button and Customer Returned Button
- Here created and Updated By will not be user id, it will be a text field. User Name or Courier

-- need to use ajax function from common js
- We need to refresh the page after updating the status
- We need to show icon with the button
- Button color will be same always with the the other button color.