<?php

// CUSTOMER-001B — customer address book + checkout address selection.

return [

    // Address book
    'index_title'   => 'My Addresses',
    'index_sub'     => 'Your address book works at every OneMember store — save an address once, use it everywhere.',
    'add_address'   => 'Add address',
    'new_title'     => 'New address',
    'edit_title'    => 'Edit address',
    'form_sub'      => 'Only the address you choose for an order is shared with that store.',
    'search'        => 'Search',
    'search_hint'   => 'Search addresses…',
    'empty_book'    => 'No saved addresses yet. Add one and never type it again.',
    'empty_search'  => 'No addresses match your search.',

    // Fields
    'field_label'        => 'Label',
    'field_label_hint'   => 'Home, Work…',
    'field_country'      => 'Country',
    'field_recipient'    => 'Recipient name',
    'field_phone'        => 'Contact phone',
    'field_line1'        => 'Address',
    'field_line1_hint'   => 'House number, street, village…',
    'field_line2'        => 'Address line 2',
    'field_building'     => 'Building',
    'field_floor'        => 'Floor',
    'field_unit'         => 'Room / Unit',
    'field_postal_code'  => 'Postcode',
    'field_landmark'     => 'Landmark',
    'field_landmark_hint' => 'Near…',
    'field_instructions' => 'Delivery instructions',

    // Administrative levels (per-country wording, config-driven)
    'level_province'     => 'Province',
    'level_district'     => 'District',
    'level_subdistrict'  => 'Subdistrict',
    'level_state_region' => 'State / Region',
    'level_township'     => 'Township',
    'level_ward_village' => 'Ward / Village',

    // Countries
    'country_TH' => 'Thailand',
    'country_MM' => 'Myanmar',

    // Suggested labels
    'label_home'    => 'Home',
    'label_work'    => 'Work',
    'label_office'  => 'Office',
    'label_parents' => 'Parents',
    'label_hotel'   => 'Hotel',
    'label_other'   => 'Other',

    // Actions & badges
    'save'           => 'Save address',
    'cancel'         => 'Cancel',
    'edit'           => 'Edit',
    'delete'         => 'Delete',
    'delete_confirm' => 'Delete this address?',
    'archive'        => 'Archive',
    'restore'        => 'Restore',
    'duplicate'      => 'Duplicate',
    'make_default'   => 'Make default',
    'set_as_default' => 'Set as my default address',
    'default_badge'  => 'Default',
    'archived_badge' => 'Archived',
    'copy_suffix'    => '(copy)',

    // Flash messages
    'saved'       => 'Address saved.',
    'deleted'     => 'Address deleted.',
    'archived'    => 'Address archived.',
    'restored'    => 'Address restored.',
    'default_set' => 'Default address updated.',
    'duplicated'  => 'Address duplicated — you are editing the copy.',
    'limit_reached' => 'You have reached the maximum number of saved addresses.',

    // Display helpers
    'floor_short' => 'Fl. :floor',
    'unit_short'  => 'Unit :unit',
    'near'        => 'Near :landmark',

    // Checkout
    'deliver_to'        => 'Deliver to',
    'checkout_add_new'  => 'Add new address',
    'checkout_save'     => 'Save this address to my address book',
    'checkout_manage'   => 'Manage addresses',
    'checkout_signin'   => 'Sign in to use your saved addresses',
    'invalid_choice' => 'That saved address is no longer available. Please choose another.',
    'choose_address' => 'Please choose a delivery address or add a new one.',

];
