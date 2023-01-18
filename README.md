# Shopping Register

Laravel based application, experiment for livewire based rendering.

## Database

### Address

Stores the addresses
- Id
- Timestamps (create, update)
- Raw - the fixed value extracted from the image. It will be used for calculating levenshtein distances

### Company

Stores a company.
- Id
- Timestamps (create, update)
- Name
- Address
- Tax number (uniq)

### Shop

Stores a shop,
- Id
- Timestamps (create, update)
- Name
- Company
- Address

### Basket

Stores the receipt main informations.
- Id
- Timestamps (create, update)
- Time of the shopping
- Shop
- Total item price
- Receipt identifier
- User identifier
TODO: add the receipt_url as optional column. Make the receipt identifier unique.

### Items

Stores an item that is bought somewhere.
- Id
- Timestamps (create, update)
- Name

### Basket items

Stores the items that were in a given basket
- Id
- Basket
- Item
- Price

## Need to be able to add/view/edit manually:
- Addresses
	- address - list
	- address?id=$id - view
	- address?id=$id&action=edit - edit
	- address?a=new - add new
- Companies
- Shops
- Items
- Baskets (with optional items)
