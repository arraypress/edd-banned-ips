## Banned IPs for Easy Digital Downloads

Safeguard your Easy Digital Downloads store by controlling who can make purchases through the targeted blocking of
specific IP addresses and user agents. Experience a significant reduction in chargebacks, fraudulent activities, and
undesirable users with ill intent.

### Features

- **Effortless IP and User Agent Management**: Seamlessly manage and control banned IP addresses and user agents.
- **Flexible Blocking Options**: Block individual IP addresses, IP ranges using CIDR notation, or entire subnet masks
  for enhanced security.
- **Enhanced Security**: Safeguard your digital products and content by restricting access.
- **Temporary Bans**: Temporarily restrict access to specific IPs or user agents to address issues without permanent
  measures.
- **Partial User Agent Matches**: Block user agents with partial matches, allowing you to effectively target unwanted
  bots and agents.

### Installation

Install the EDD - Banned IPs plugin using one of these methods:

**From WordPress.org:**

1. Go to your WordPress admin dashboard.
2. Navigate to "Plugins" -> "Add New".
3. Search for "EDD - Banned IPs".
4. Click "Install Now" and then "Activate".

**From GitHub:**

1. Clone the GitHub repository: `https://github.com/arraypress/edd-banned-ips.git`
2. Alternatively, download it as a ZIP
   file: [Download ZIP](https://github.com/arraypress/edd-banned-ips/archive/refs/heads/main.zip)

### Usage

After activation, EDD - Banned IPs seamlessly integrates with your Easy Digital Downloads settings, providing new
options to strengthen your store's security:

#### Banned IP Settings

- Configure settings to prevent specific IP addresses from making purchases.
- Valuable for temporarily blocking problematic customers.

#### Banned IPs List

- Add IP addresses to prevent purchases from those addresses.
- Each IP address, IPv4 (e.g., 192.168.1.1) or IPv6 (e.g., 2001:0db8:85a3:0000:0000:8a2e:0370:7334), should be on a
  separate line.

#### Banned User Agents

- Block user agents to prevent purchases from those agents.
- Enter user agent strings to block, with an optional suffix of `**` for partial matches.

#### Allow Existing Customers

- Enable to grant existing customers permission to bypass the IP check.

#### Custom Banned IP Message

- Personalize the message displayed to customers attempting purchases from banned IP addresses.

You can access the plugin settings from Downloads > Settings > Payments > Checkout or by clicking "Settings" on the
plugin list under the plugin title.

Secure your EDD store by installing EDD - Banned IPs. Simply install the plugin via your WordPress dashboard and
configure the settings under the EDD Checkout section.

### Customization - Hooks and Filters

To tailor the plugin's behavior to your specific requirements, you can utilize hooks and filters. Here are some of the
most important filters available:

## Customization - Hooks and Filters

To tailor the plugin's behavior to your specific requirements, you can utilize hooks and filters. Here are some of the
most important filters available:

Filter: **edd_is_ip_banned**

Determine if an IP address is banned or not.
Example:

```php
function custom_is_ip_banned( $return, $ip ) {
    if ( $ip === '192.168.1.100' ) {
        $return = true; // Ban specific IP address
    }
    return $return;
}
add_filter( 'edd_is_ip_banned', 'custom_is_ip_banned', 10, 2 );
```

Function: **edd_get_banned_ips**

Retrieve the list of banned IP addresses.
Example:

```php
function custom_get_banned_ips( $ips ) {
    $ips[] = '203.0.113.50'; // Add an additional banned IP address
    return $ips;
}
add_filter( 'edd_get_banned_ips', 'custom_get_banned_ips' );
```

Filter: **edd_is_user_agent_banned**

Determine if a user agent is banned or not.
Example:

```php
function custom_is_user_agent_banned( $return, $user_agent ) {
    if ( strpos( $user_agent, 'bad-bot' ) !== false ) {
        $return = true; // Ban user agents containing 'bad-bot'
    }
    
    return $return;
}
add_filter( 'edd_is_user_agent_banned', 'custom_is_user_agent_banned', 10, 2 );
```

Function: **edd_get_banned_user_agents**

Retrieve the list of banned user agents.
Example:

```php
function custom_get_banned_user_agents( $user_agents ) {
    $user_agents[] = 'harmful-user-agent'; // Add an additional banned user agent
    return $user_agents;
}
add_filter( 'edd_get_banned_user_agents', 'custom_get_banned_user_agents' );
```
## Support

For assistance or questions, visit the [support page](https://wordpress.org/support/plugin/edd-banned-ips) on WordPress.org.

## Contributions

Contributions to this plugin are welcome. Raise issues on GitHub or submit pull requests for bug fixes or new features. Share feedback and suggestions for enhancements.

## License

This plugin is licensed under the [GNU General Public License v2.0](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html).