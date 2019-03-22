METHODS:

1.gateway() = generates new payment gateway based on seller data.

2.checkout() = makes payment and inserts data into collaboration and transactions tables
(sets subscribed flag to true and admin can use app normally).

3.checkSubscriptionDate() = compares todays date and end of subscription date, if subscription is expired
sets subscribed flag to false and admin can no longer use app until he pays subscription again
