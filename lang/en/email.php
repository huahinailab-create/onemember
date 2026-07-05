<?php

return [

    // Welcome
    'welcome_subject'  => 'Welcome to :name',
    'welcome_heading'  => 'Welcome to :name!',
    'welcome_body_1'   => 'Hi :name, your account has been created successfully.',
    'welcome_body_2'   => 'Start building your customer loyalty program and keep members coming back.',
    'welcome_cta'      => 'Go to Dashboard',
    'welcome_footer'   => 'If you did not create this account, please contact support.',

    // Email Verified
    'verified_subject' => 'Your email has been verified',
    'verified_heading' => 'Email Verified',
    'verified_body_1'  => 'Hi :name, your email address has been verified.',
    'verified_body_2'  => 'You now have full access to your OneMember account.',
    'verified_cta'     => 'Go to Dashboard',

    // Trial Started
    'trial_started_subject'  => 'Your free trial has started',
    'trial_started_heading'  => 'Your Free Trial Has Started',
    'trial_started_body_1'   => 'Hi :name, welcome aboard! Your 14-day free trial is now active.',
    'trial_started_body_2'   => 'Your trial ends on :date.',
    'trial_started_body_3'   => 'Explore all features — no credit card required during the trial.',
    'trial_started_cta'      => 'Explore Now',

    // Trial Ending
    'trial_ending_subject'  => 'Your trial ends in :days day(s)',
    'trial_ending_heading'  => 'Your Trial is Ending Soon',
    'trial_ending_body_1'   => 'Hi :name, your free trial ends in :days day(s).',
    'trial_ending_body_2'   => 'Trial end date: :date.',
    'trial_ending_body_3'   => 'Upgrade now to keep access and retain your data.',
    'trial_ending_cta'      => 'Upgrade Now',

    // Subscription Purchased
    'subscription_purchased_subject' => 'Subscription confirmed',
    'subscription_purchased_heading' => 'Subscription Confirmed',
    'subscription_purchased_body_1'  => 'Hi :name, your :plan subscription is now active.',
    'subscription_purchased_cta'     => 'View Subscription',

    // Subscription Renewed
    'subscription_renewed_subject' => 'Subscription renewed',
    'subscription_renewed_heading' => 'Subscription Renewed',
    'subscription_renewed_body_1'  => 'Hi :name, your :plan subscription has been renewed.',
    'subscription_renewed_cta'     => 'View Subscription',

    // Subscription Cancelled
    'subscription_cancelled_subject' => 'Subscription cancelled',
    'subscription_cancelled_heading' => 'Subscription Cancelled',
    'subscription_cancelled_body_1'  => 'Hi :name, your subscription has been cancelled.',
    'subscription_cancelled_body_2'  => 'You will keep access until :date.',
    'subscription_cancelled_body_3'  => 'You can reactivate your subscription at any time.',
    'subscription_cancelled_cta'     => 'Reactivate',

    // Payment Failed
    'payment_failed_subject' => 'Payment failed — action required',
    'payment_failed_heading' => 'Payment Failed',
    'payment_failed_body_1'  => 'Hi :name, we were unable to process your payment.',
    'payment_failed_body_2'  => 'Please update your payment method to avoid service interruption.',
    'payment_failed_cta'     => 'Update Payment Method',

    // Password Changed
    'password_changed_subject' => 'Your password has been changed',
    'password_changed_heading' => 'Password Changed',
    'password_changed_body_1'  => 'Hi :name, your account password was recently changed.',
    'password_changed_body_2'  => 'If you did not make this change, please secure your account immediately.',
    'password_changed_cta'     => 'Secure My Account',

    // Feedback Thank-you
    'feedback_thankyou_subject' => 'Thanks for your feedback',
    'feedback_thankyou_heading' => 'Thank You for Your Feedback',
    'feedback_thankyou_body_1'  => 'Hi :name, we received your feedback.',
    'feedback_thankyou_body_2'  => 'Category: :category',
    'feedback_thankyou_body_3'  => 'Our team reviews all feedback. We may follow up if we need more details.',

    // Feedback Support Notification
    'feedback_support_subject'  => 'New :category feedback received',
    'feedback_support_heading'  => 'New Feedback Submitted',

    // Shared labels
    'label_plan'         => 'Plan',
    'label_next_billing' => 'Next billing date',
    'label_invoice'      => 'Invoice ID',
    'label_amount'       => 'Amount due',
    'label_from'         => 'From',
    'label_category'     => 'Category',
    'label_subject'      => 'Subject',
    'label_user_id'      => 'User ID',
    'label_merchant_id'  => 'Merchant ID',
    'label_submitted_at' => 'Submitted at',
    'label_url'          => 'URL',
    'powered_by'         => 'Powered by :app',


    // Member notifications (MVP-006)
    'member_points_earned_subject'  => 'You earned points at :merchant',
    'member_points_earned_heading'  => 'You Earned Points!',
    'member_points_earned_body_1'   => 'Hi :name, thank you for your purchase at :merchant.',
    'member_points_earned_points'   => 'Points earned: +:points',
    'member_points_earned_balance'  => 'Your new balance: :balance points',
    'member_points_earned_body_2'   => 'Keep collecting points to unlock rewards. See you again soon!',

    'member_reward_redeemed_subject' => 'Your reward from :merchant',
    'member_reward_redeemed_heading' => 'Reward Redeemed!',
    'member_reward_redeemed_body_1'  => 'Hi :name, you redeemed ":reward" at :merchant.',
    'member_reward_redeemed_points'  => 'Points used: :points',
    'member_reward_redeemed_balance' => 'Your remaining balance: :balance points',
    'member_reward_redeemed_body_2'  => 'Enjoy your reward — and thank you for being a loyal member!',

    'member_birthday_subject' => 'Happy Birthday from :merchant!',
    'member_birthday_heading' => 'Happy Birthday!',
    'member_birthday_body_1'  => 'Hi :name, everyone at :merchant wishes you a wonderful birthday.',
    'member_birthday_points'  => 'Birthday bonus: +:points points',
    'member_birthday_body_2'  => 'Your bonus points have been added to your balance. Treat yourself!',


    // Win-back alerts (MVP-008)
    'winback_subject'        => ':count member(s) may need a win-back nudge',
    'winback_heading'        => 'Members Slipping Away',
    'winback_body_1'         => 'Hi :name, :count member(s) have not visited in :days days:',
    'winback_col_member'     => 'Member',
    'winback_col_phone'      => 'Phone',
    'winback_col_last_visit' => 'Last Visit',
    'winback_body_2'         => 'A friendly message or a small bonus offer can bring them back.',
    'winback_cta'            => 'View Members',

];
