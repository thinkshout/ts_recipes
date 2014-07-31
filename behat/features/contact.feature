Feature: Contact form
  In order to ask questions and provide feedback
    As a site visitor
    I need to use the contact form

Scenario: Submits feedback when required fields are filled out
  Given I am on "/"
  When I follow "Contact Us"
    And I fill in "Your name" with "Test User"
    And I fill in "Your e-mail address" with "visitor@example.com"
    And I fill in "Subject" with "Great new site"
    And I fill in "Message" with "I especially liked the animated gif"
    And I press "Send message"
  Then I should see "Your message has been sent."
