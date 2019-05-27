Task:
There is an external API with information about discount in %: http://magento-test.demo.noveogroup.com/api.php
The discount can be 0 - 30% and the value doesn’t change during the day

It is necessary to develop a Magento 2 extension, which will automatically apply the discount above to all products in the users’ shopping cart.

Back-office should allow to configure the extension settings:
specify the period of day, when the discount is applied (for example, from 9:30 till 17:00),
configure the URL of API,
enable/disable applying of the discount.
