<?php

return [
    // Merchant scan-to-join
    'add_button'          => 'Add Existing OneMember Member',
    'add_title'           => 'Add Existing OneMember Member',
    'scan_heading'        => 'Scan the customer\'s OneMember Card',
    'scan_hint'           => 'Ask the customer to open their OneMember Card, then scan the QR code (or paste the scanned code below).',
    'scan_label'          => 'Scanned QR code',
    'scan_privacy_note'   => 'The QR contains only a secure OneMember ID — no personal data.',
    'scan_submit'         => 'Find OneMember Identity',
    'error_invalid_qr'    => 'This QR code is not a valid OneMember Card.',
    'error_already_member'=> 'This customer is already a member of your programme.',
    'error_name_required' => 'The name field is required to create a membership.',

    // Consent screen (customer-facing, on the merchant device)
    'consent_title'       => 'Customer Consent',
    'consent_heading'     => 'Join :merchant?',
    'consent_phone_hint'  => 'Verified phone: :phone',
    'consent_body'        => ':merchant is asking to add you as a member and requests the following information from your OneMember Identity. Untick anything you don\'t want to share — only approved fields are given to this merchant.',
    'field_name'          => 'Name',
    'field_phone'         => 'Mobile number',
    'field_email'         => 'Email',
    'field_birthday'      => 'Birthday (for birthday rewards)',
    'field_postal_code'   => 'Postal code',
    'field_required'      => 'required',
    'consent_footnote'    => 'Your loyalty points at other shops stay completely separate. This merchant never sees your other memberships. You can review your choices at any time.',
    'consent_approve'     => 'I approve — join now',
    'consent_decline'     => 'Cancel',
    'join_success'        => 'Member added from OneMember Identity :id — no duplicate account, no re-typing.',

    // OneMember Card
    'card_title'          => 'OneMember Card',
    'card_label'          => 'Identity Card',
    'card_scan_hint'      => 'Show this QR at any OneMember shop to join in seconds.',
    'card_privacy'        => 'This QR contains a secure token only — never your personal data.',
];
