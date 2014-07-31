Feature: Log in and out of the site
  In order to maintain an account
    As a site visitor
    I need to log in and out of the site.

Scenario: Logs in to the site
  Given I am on "/"
  When I follow "Log In"
    And I fill in "E-mail" with "admin"
    And I fill in "Password" with "test"
    And I press "Log in"
  Then I should see "Log out"
    And I should see "My account"

# Require a real browser. Will use Selenium/Firefox (or Zombie or Sahi).
@javascript
Scenario: Logs out of the site
  Given I am on "/"
  When I follow "Log In"
    And I fill in "E-mail" with "admin"
    And I fill in "Password" with "test"
    And I press "Log in"
    And I follow "Log out"
  Then I should see "Log in"
    And I should not see "My account"
