<?php

//https://github.com/drush-ops/drush/blob/7.x/commands/pm/updatestatus.pm.inc
//use Pantheon\Terminus\Commands\TerminusCommand;
use Pantheon\Terminus\Commands\Site\SiteCommand;

class SecurityUpdatesCommand extends SiteCommand {

  /**
   * Displays security updates for all sites
   *
   * @authorize
   *
   * @command security-updates-report
   *
   * @option environment Pantheon environment
   * @option email Send a report
   * @option team Team-only filter
   * @option owner Owner filter; "me" or user UUID
   * @option org Organization filter; "all" or organization UUID
   * @option name Name filter
   *
   * @usage terminus security-updates-report
   *     Displays the list of all sites accessible to the currently logged-in user.
   * @usage terminus security-updates-report --team
   *     Displays the list of sites of which the currently logged-in user is a member of the team.
   * @usage terminus security-updates-report --owner=<user>
   *     Displays the list of accessible sites owned by the user with UUID <user>.
   * @usage terminus security-updates-report --owner=me
   *     Displays the list of sites owned by the currently logged-in user.
   * @usage terminus security-updates-report --org=<org>
   *     Displays a list of accessible sites associated with the <org> organization.
   * @usage terminus security-updates-report --org=all
   *     Displays a list of accessible sites associated with any organization of which the currently logged-in is a member.
   * @usage terminus security-updates-report --name=<regex>
   *     Displays a list of accessible sites with a name that matches <regex>.
   */

  public function securityUpdates($options = [
    'environment' => "dev",
    'email' => NULL,
    'team' => FALSE,
    'owner' => NULL,
    'org' => NULL,
    'name' => NULL,
  ]) {

    // Get list of sites
    $this->sites()->fetch(
      [
        'org_id' => isset($options['org']) ? $options['org'] : NULL,
        'team_only' => isset($options['team']) ? $options['team'] : FALSE,
      ]
    );
    if (isset($options['name']) && !is_null($name = $options['name'])) {
      $this->sites->filterByName($name);
    }
    if (isset($options['owner']) && !is_null($owner = $options['owner'])) {
      if ($owner == 'me') {
        $owner = $this->session()->getUser()->id;
      }
      $this->sites->filterByOwner($owner);
    }
    $sites = $this->sites->serialize();

    if (empty($sites)) {
      $this->log()->notice('You have no sites.');
      return;
    }

    // Prevent SSL errors
    $ssl_options = [
      "ssl" => [
        "verify_peer" => FALSE,
        "verify_peer_name" => FALSE,
      ],
    ];

    // A place to store the results for all sites
    $all_sites_array = [];

    foreach ($sites as $site) {

      print "getting update details for site: " . $site['name'] . "\n"; //DEBUGGING

      // A place to store the results for the site being processed
      $site_array = [];
      $site_array['name'] = $site['name'];

      if ($site['frozen'] === 'true') {
        $site_array['message'] = "site frozen";
        $all_sites_array[] = $site_array;
        continue;
      }
      $environment = $this->getSite($site['name'])->getEnvironments()->get($options['environment'])->serialize();
      if ($environment['initialized'] == 'false') {
        $site_array['message'] = "environment not initialized";
        $all_sites_array[] = $site_array;
        continue;
      }
      if ($environment['locked'] === true) {
        $site_array['message'] = "environment is locked";
        $all_sites_array[] = $site_array;
        continue;
      }

      $command = "terminus drush {$site['name']}.{$options['environment']} -- pm-updatestatus --security-only --format=php";
      $response = $this->pipe_exec($command);

      //looks like we get stderr in stdout so we get debug stuff in our serialized response. Quick fix for now.
      $last_debug_line = strrpos($response[1], "Checking available update data");
      if ($last_debug_line !== false) {
        $last_debug_line_newline = strpos($response[1], "\n", $last_debug_line);
        if ($last_debug_line_newline !== false) {
          $response[1] = substr($response[1], $last_debug_line_newline);
        }
      }
      $start_char = strpos ( $response[1] , "a:");
      if ($start_char !== false) {
        $response[1] = substr($response[1], $start_char );
        $site_array['results'] = unserialize($response[1]);
      }
      else if (strpos($response[1], "[error]") === FALSE) {
        $site_array['message'] = "no security updates available";
        $all_sites_array[] = $site_array;
        continue;
      }
      else {
        if (strpos($response[1], "Drush command terminated abnormally due to an unrecoverable error") !== FALSE) {
          $site_array['message'] = "Drush command terminated abnormally due to an unrecoverable error";
          $all_sites_array[] = $site_array;
          continue;
        }
        else if (strpos($response[1], "The drush command 'pm-updatestatus' could not be found.") !== FALSE) {
          $site_array['message'] = "site uses incorrect drush version? (pm-updatestatus could not be found)";
          $all_sites_array[] = $site_array;
          continue;
        }
        else {
          $site_array['message'] = "unknown error";
          $all_sites_array[] = $site_array;
          continue;
        }
      }

      foreach ($site_array['results'] as &$module) {
        foreach ($module['security updates'] as $release) {

          // A place to store the security advisories for the module being processed
          $security_advisory = [];

          // find changelog description and parse it for security advisory link and risk level
          $release_content = file_get_contents($release['release_link'], FALSE, stream_context_create($ssl_options));

          if (isset($release_content)) {

            // find security advisory url in changelog description
            // TODO: there can probably be more than one security advisory per release, catch all.
            preg_match('/<a href=\"(.*)\" .*>.*SA-.*<\/a>/', $release_content, $security_advisory_url_match);
            if (isset($security_advisory_url_match[1])) {
              $security_advisory['security_advisory_version'] = $release['version'];
              $security_advisory['security_advisory_url'] = $security_advisory_url_match[1];
              if (strpos($security_advisory['security_advisory_url'], 'drupal.org') === false) {
                $security_advisory['security_advisory_url'] = "https://www.drupal.org" . $security_advisory['security_advisory_url'];
              }
              // find security risk level on security advisory page
              $sa_url_contents = file_get_contents($security_advisory['security_advisory_url'], FALSE, stream_context_create($ssl_options));
              preg_match('/<a href=\"https:\/\/www\.drupal\.org\/security-team\/risk-levels\">.*\">(.*)<\/span>/', $sa_url_contents, $security_advisory_level_match);
              if (isset($security_advisory_level_match[1])) {
                $security_advisory['security_advisory_level'] = trim($security_advisory_level_match[1]);
              }
              else {
                $security_advisory['security_advisory_level'] = "unknown";
              }
              $module['security_advisories'][] = $security_advisory;
            }
          }
        }
      }
      $all_sites_array[] = $site_array;
    }

    // Create table for sites that have security updates
    $all_sites_string = "<h2>Sites with security updates</h2><table border='1'><tr><td>Site</td><td>Module</td><td>Installed</td><td>Proposed</td><td>Security Advisories</td></tr>";
    foreach ($all_sites_array as $site_array) {
      if (!isset($site_array['message'])) {
        if (isset($site_array['results'])) {
          foreach ($site_array['results'] as $module) {
            $all_sites_string .= "<tr>";
            $all_sites_string .= "<td>{$site_array['name']}</td>";
            $all_sites_string .= "<td>{$module['name']}</td>";
            if (isset($module['existing_version'])) {$all_sites_string .= "<td>{$module['existing_version']}</td>";}
            if (isset($module['recommended'])) {$all_sites_string .= "<td>{$module['recommended']}</td>";}
            if (isset($module['security_advisories'])) {
              $all_sites_string .= "<td>";
              $counter = 0;
              foreach ($module['security_advisories'] as $security_advisory) {
                $counter++;
                $security_advisory_string = "";
                if (isset($security_advisory['security_advisory_url']) && isset($security_advisory['security_advisory_level'])) {
                  $security_advisory_string = "<a href='{$security_advisory['security_advisory_url']}'>{$security_advisory['security_advisory_level']}</a>";
                }
                if ($counter > 1) {$all_sites_string .= " / ";}
                $all_sites_string .= $security_advisory_string;
              }
              $all_sites_string .= "</td>";
            }
          }
        }
        $all_sites_string .= "</tr>";
      }
    }
    $all_sites_string .= "</table>";
    print ($all_sites_string);

    // Create table for sites that had errors
    $all_sites_string_errors = "<h2>Sites with errors</h2><table border='1'><tr><td>Site</td><td>Message</td></tr>";
    foreach ($all_sites_array as $site_array) {
      if (isset($site_array['message']) && $site_array['message'] != "no security updates available") {
        $all_sites_string_errors .= "<tr>";
        $all_sites_string_errors .= "<td>{$site_array['name']}</td>";
        $all_sites_string_errors .= "<td>{$site_array['message']}</td>";
        $all_sites_string_errors .= "</tr>";
      }
    }
    $all_sites_string_errors .= "</table>";
    print ($all_sites_string_errors);

    // Create table for sites that do not have security updates
    $all_sites_string_no_updates = "<h2>Sites with no security updates</h2><table border='1'><tr><td>Site</td></tr>";
    foreach ($all_sites_array as $site_array) {
      if (isset($site_array['message']) && $site_array['message'] == "no security updates available") {
        $all_sites_string_no_updates .= "<tr>";
        $all_sites_string_no_updates .= "<td>{$site_array['name']}</td>";
        $all_sites_string_no_updates .= "</tr>";
      }
    }
    $all_sites_string_no_updates .= "</table>";
    print ($all_sites_string_no_updates);

//    $to = 'invalid'; // comma separated
//    $subject = 'Security Updates Report';
//    $message = '<html><body>{$all_sites_string}</body></html>';
//    $headers[] = 'MIME-Version: 1.0';
//    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
//    // Additional headers
//    // $headers[] = 'To: Invalid <invalid.com>, Invalid <invalid.com>';
//    $headers[] = 'From: Invalid <invalid.com>';
//    mail($to, $subject, $message, implode("\r\n", $headers));
  }

  function pipe_exec($cmd, $input = '') {
    $proc = proc_open($cmd, [
      ['pipe', 'r'],
      ['pipe', 'w'],
      ['pipe', 'w'],
    ], $pipes);
    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $return_code = (int) proc_close($proc);

    return [$return_code, $stdout, $stderr];
  }

}
