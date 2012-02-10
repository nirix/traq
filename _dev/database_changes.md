Multiple tables
-----------

- Converted timestamp fields to DATETIME

Tickets table
--------------

- Dropped user_name field
- Appended _id to fields:
    - milestone
    - version
    - component
    - type
    - status
    - priority
    - severity
    - assigned_to
- Prepended is_ to the closed and private fields
- Appended _at to the created and updated fields

Timeline table
---------------

- Dropped date field

Dropped tables
--------------

- repositories