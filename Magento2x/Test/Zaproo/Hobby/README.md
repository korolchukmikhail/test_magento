DONE - Add a custom customer attribute “Hobby“ with possible options: “Yoga“, “Traveling“, “Hiking“. The attribute is not required.
DONE - Add a possibility to fetch / edit the attribute using GraphQL.
DONE - Admin must be able to edit the attribute in admin panel.
DONE - Add a link in the customer account menu.
DONE - The link must lead on the page “Edit Hobby“. There must be a form with one field “Hobby“ and submit button.
DONE - “Hobby“ must be displayed in the top right corner in the format “Hobby: %s“ and must be correspond to the current customer hobby. Place it right away after the welcome message.

NB! Notice that it must work correctly with all enabled Magento caches

GraphQL:

mutation {
  hobby(input: "Yoga")
}