<?php

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
//      continue; //DEBUGGING
//      if ($site['name'] != "givingforum") {
//        continue;
//      }

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

      $command = "terminus remote:drush {$site['name']}.{$options['environment']} -- pm-updatecode --security-only -n";
      $response = $this->pipe_exec($command);

      if (strpos($response[1], "Drush command terminated abnormally due to an unrecoverable error") !== FALSE) {
        $site_array['message'] = "Drush command terminated abnormally due to an unrecoverable error";
        $all_sites_array[] = $site_array;
        continue;
      }
      if (strpos($response[1], "Command pm-updatecode needs the following modules installed/enabled") !== FALSE) {
        $site_array['message'] = "update module not enabled on this site";
        $all_sites_array[] = $site_array;
        continue;
      }
      if (strpos($response[1], "This codebase is assembled with Composer instead of Drush.") !== FALSE) {
        $site_array['message'] = "site uses Composer instead of Drush";
        $all_sites_array[] = $site_array;
        continue;
      }
      if (strpos($response[1], "No security updates available.") !== FALSE && strpos($response[1], "SECURITY UPDATE available") === FALSE) {
        // result can say 'No security updates available.' and also include security updates hence this extra check
        $site_array['message'] = "no security updates available";
        $all_sites_array[] = $site_array;
        continue;
      }
      if (strpos($response[1], "SECURITY UPDATE available") === FALSE) {
        $site_array['message'] = "unknown error";
        $all_sites_array[] = $site_array;
        continue;
      }

      // Find column dimensions
      preg_match('/.*(\bName +Installed.*Proposed.*Message\b).*/', $response[1], $match);
      if (isset($match[0])) {

        $name_start = strpos($match[0], 'Name');
        $installed_version_start = strpos($match[0], 'Installed');
        $proposed_version_start = strpos($match[0], 'Proposed');
        $message_start = strpos($match[0], 'Message');

        $name_length = $installed_version_start - $name_start;
        $installed_version_length = $proposed_version_start - $installed_version_start;
        $proposed_version_length = $message_start - $proposed_version_start;

        // Grab all security updates
        // regex below is because of situations like this
        //  Name    Installed Version  Proposed version  Message
        //  Drupal  8.3.1              8.4.0             Do not update via Drush. See:
        //                                               https://pantheon.io/docs/articles
        //                                               /sites/code/applying-upstream-upd
        //                                               ates/ (SECURITY UPDATE available)
        preg_match_all('/.*Do not update via Drush..*|.*(?<!ates\/ \()SECURITY UPDATE available.*/', $response[1], $matches);

        if (!isset($matches[0])) {
          $site_array['message'] = "error, unexpected security notifications format";
          $all_sites_array[] = $site_array;
          continue;
        }

        foreach ($matches[0] as $match) {

          // A place to store the results for the security update for the site being processed
          $site_update_array = [];

          // Get or create machine name
          $name = trim(substr($match, $name_start, $name_length));
          if (empty($name)) {
            $site_update_array['message'] = "name not detected";
            $site_array['updates'][] = $site_update_array;
            continue;
          }
          preg_match('/.*\((.*)\)/', $name, $machine_name_match);
          if (isset($machine_name_match[1])) {
            $machine_name = $machine_name_match[1];
          }
          else {
            $machine_name = strtolower(str_replace(' ', '', $name));
          }
          $site_update_array['machine_name'] = $machine_name;

          // Get installed version
          $installed_version = trim(substr($match, $installed_version_start, $installed_version_length));
          // matches 8.3.1 or 7.x-3.1
          preg_match('/([^-]+)-(\d+)+\.*(\d+)|(\d+).(\d+)+\.*(\d+)/', $installed_version, $version_match);
          if (!$version_match) {
            $site_update_array['message'] = "minor version not detected";
            $site_array['updates'][] = $site_update_array;
            continue;
          }
          if (!isset($version_match[4])) {
            $installed_version_core = $version_match[1];
            $installed_version_major = $version_match[2];
            $installed_version_minor = $version_match[3];
          }
          else {
            $installed_version_core = $version_match[4];
            $installed_version_major = $version_match[5];
            $installed_version_minor = $version_match[6];
          }

          $site_update_array['installed_version'] = $installed_version;

          // Get proposed version
          $proposed_version = trim(substr($match, $proposed_version_start, $proposed_version_length));
          preg_match('/([^-]+)-(\d+)+\.*(\d+)|(\d+).(\d+)+\.*(\d+)/', $proposed_version, $version_match);
          if (!isset($version_match[4])) {
            $proposed_version_core = $version_match[1];
            $proposed_version_major = $version_match[2];
            $proposed_version_minor = $version_match[3];
          }
          else {
            $proposed_version_core = $version_match[4];
            $proposed_version_major = $version_match[5];
            $proposed_version_minor = $version_match[6];
          }
          $site_update_array['proposed_version'] = $proposed_version;

          // We don't support a major version change yet, for 8.3.1 -> 8.4.1 it would be nice to add this.
          if ($proposed_version_major != $installed_version_major) {
            $site_update_array['message'] = "major version update detected";
            $site_array['updates'][] = $site_update_array;
            continue;
          }

          // find url for project changelog rss page on releases page for drupal.org project
          $releases_url = "https://www.drupal.org/project/{$machine_name}/releases";
          $releases_content = file_get_contents($releases_url, FALSE, stream_context_create($ssl_options));
          preg_match('/<div id="feeds">.*<a href="([^ ]*)"/', $releases_content, $releases_url_match);

          // Can't find changelog rss page url
          if (!isset($releases_url_match[1])) {
            $site_update_array['message'] = "unable to find project changelog rss page";
            $site_array['updates'][] = $site_update_array;
            continue;
          }
          $site_update_array['releases_url'] = $releases_url;

          // Get content for changelog rss page
          $releases_rss_content = file_get_contents($releases_url_match[1], FALSE, stream_context_create($ssl_options));
          $xml = new SimpleXMLElement($releases_rss_content);

          // loop through minor versions (current + 1 to new)
          for ($check_minor_version = $installed_version_minor + 1; $check_minor_version <= $proposed_version_minor; $check_minor_version++) {

            // A place to store the results for the minor version of the security update for the site being processed
            $site_update_version_array = [];

            // find url of release
            $release_version_url = $xml->xpath("//channel/item[title/text() = '{$machine_name} {$installed_version_core}-{$installed_version_major}.{$check_minor_version}']/link/text()");
            if (isset($release_version_url[0])) {
              $site_update_version_array['release_url'] = (string) $release_version_url[0];
            }

            // find changelog description and parse it for security advisory link and risk level
            $item_description = $xml->xpath("//channel/item[title/text() = '{$machine_name} {$installed_version_core}-{$installed_version_major}.{$check_minor_version}']/description/text()");
            if (isset($item_description[0][0])) {

              // find security advisory url in changelog description
              // TODO: there can probably be more than one security advisory per release, catch all, not one.
              $item_description = html_entity_decode($item_description[0][0]);
              preg_match('/<a href=\"(.*)\" .*>SA-.*<\/a>/', $item_description, $security_advisory_url_match);
              if (isset($security_advisory_url_match[1])) {
                $site_update_version_array['security_advisory_url'] = $security_advisory_url_match[1];

                // find security risk level on security advisory page
                $sa_url_contents = file_get_contents($security_advisory_url_match[1], FALSE, stream_context_create($ssl_options));
                preg_match('/<a href=\"https:\/\/www\.drupal\.org\/security-team\/risk-levels\">.*\">(.*)<\/span>/', $sa_url_contents, $security_advisory_level_match);
                if (isset($security_advisory_level_match[1])) {
                  $site_update_version_array['security_advisory_level'] = trim($security_advisory_level_match[1]);
                }
              }
            }
            $site_update_array['versions'][] = $site_update_version_array;
          } // END loop through minor versions
          $site_array['updates'][] = $site_update_array;
        }
      }
      $all_sites_array[] = $site_array;
    }
    print_r($all_sites_array);
    print ("\n" . "\n");

    // Create table for sites that have security updates
    $all_sites_string = "<h2>Sites with security updates</h2><table border='1'><tr><td>Site</td><td>Module</td><td>Installed</td><td>Proposed</td><td>Releases</td></tr>";
    foreach ($all_sites_array as $site_array) {
      if (!isset($site_array['message'])) {
        if (isset($site_array['updates'])) {
          foreach ($site_array['updates'] as $update) {
            $all_sites_string .= "<tr>";
            $all_sites_string .= "<td>{$site_array['name']}</td>";
            $all_sites_string .= "<td>{$update['machine_name']}</td>";
            $all_sites_string .= "<td>{$update['installed_version']}</td>";
            $all_sites_string .= "<td>{$update['proposed_version']}</td>";
            if (isset($update['versions'])) {
              $all_sites_string .= "<td>";
              $counter = 0;
              foreach ($update['versions'] as $version) {
                if (isset($version['release_url'])) {
                  $counter++;
                  $release_version = substr($version['release_url'], strrpos($version['release_url'], "/", -1) + 1);
                  $security_advisory = "";
                  if (isset($version['security_advisory_url']) && isset($version['security_advisory_level'])) {
                    $security_advisory = " (<a href='{$version['security_advisory_url']}'>{$version['security_advisory_level']}</a>)";
                  }
                  if ($counter > 1) {$all_sites_string .= " / ";}
                  $all_sites_string .= "<a href='{$version['release_url']}'>{$release_version}</a>{$security_advisory}";
                }
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
