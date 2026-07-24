# Rest API - ILIAS Plugin

**Table of Contents**

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Compatibility](#compatibility)
- [Installation](#installation)
    - [Apache Configuration](#apache-configuration)
    - [Nginx Configuration](#nginx-configuration)
    - [.htaccess Alternative](#htaccess-alternative)
    - [Plugin Installation](#plugin-installation)
- [Activation](#activation)
- [Usage](#usage)
    - [Authentication & Authorization](#authentication--authorization)
    - [API Calls](#api-calls)
- [Documentation](#documentation)

## Introduction

This plugin enables an interface access in the REST paradigm. It provides endpoints to interact with various ILIAS components, including:

- **User**: Manage users and check their existence.
- **Course**: Manage courses and course memberships.
- **Group**: Manage groups and group memberships.
- **Category**: Manage categories.
- **Repository**: Access repository objects and metadata.
- **Test**: Access test data and results.

## Requirements

- Composer
- Webserver (Apache or Nginx)
- PHP 8.1+

### Compatibility
| Plugin Branches | ILIAS Versions | PHP Versions |
|-----------------|----------------|--------------|
| release_9       | 9              | 8.1 - 8.2    |
| release_10      | 10             | 8.2 - 8.3    |


## Installation

### Apache Configuration

To route requests to the API connector, you need to add rewrite rules to your VirtualHost configuration.

**Configuration locations:**
- **Debian/Ubuntu:** `/etc/apache2/sites-enabled/000-default.conf` (or your specific site config)
- **RHEL/CentOS/Fedora:** `/etc/httpd/conf.d/` (e.g., `/etc/httpd/conf.d/ilias.conf`)

Add the following block inside your `<VirtualHost>` or `<Directory /var/www/html>` section:

```apacheconf
<Directory /var/www/html>
    # Enable the rewrite engine
    RewriteEngine On

    # Set the HTTP_AUTHORIZATION header into an environment variable. 
    # This is required for PHP-FPM / FastCGI to support Basic Auth.
    RewriteCond %{HTTP:Authorization} ^(.*)
    RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]

    # Route all requests starting with /api/ to the plugin's connector script.
    # The [L] flag means this is the last rule if it matches.
    RewriteRule ^/?api/(.*) /public/Customizing/global/plugins/Services/EventHandling/EventHook/RestAPI/src/connector.php [L]
</Directory>
```

After changing the configuration, restart or reload Apache:
- **Debian/Ubuntu:** `systemctl reload apache2`
- **RHEL/CentOS/Fedora:** `systemctl reload httpd`

### Nginx Configuration

For Nginx, you need to add a location block to your server configuration.

**Configuration locations:**
- **Debian/Ubuntu:** `/etc/nginx/sites-enabled/default` (or your specific site config)
- **RHEL/CentOS/Fedora:** `/etc/nginx/conf.d/`

Add the following block inside your `server { ... }` block:

```nginx
# Handle requests to the REST API
location /api/ {
    # Route the request to the connector.php script.
    # We use fastcgi_param to pass the path information.
    rewrite ^/api/(.*)$ /public/Customizing/global/plugins/Services/EventHandling/EventHook/RestAPI/src/connector.php last;
}

# Ensure the connector.php is handled by PHP-FPM
location ~ /public/Customizing/global/plugins/Services/EventHandling/EventHook/RestAPI/src/connector\.php$ {
    include snippets/fastcgi-php.conf; # or your fastcgi configuration
    fastcgi_pass unix:/run/php/php-fpm.sock; # adjust to your PHP-FPM socket/port
    
    # Ensure Authorization header is passed to PHP
    fastcgi_param HTTP_AUTHORIZATION $http_authorization;
}
```

After changing the configuration, reload Nginx: `systemctl reload nginx`

### .htaccess Alternative

If you do not have access to the global server configuration, you can use a `.htaccess` file in the ILIAS root directory. Ensure that `AllowOverride All` (or at least `FileInfo`) is enabled for your directory in the Apache config.

Create or edit `.htaccess` in the ILIAS root:

```apacheconf
RewriteEngine On

# Fix for Authorization Header in FPM/FastCGI environments
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]

# Route /api/ requests
RewriteRule ^/?api/(.*) public/Customizing/global/plugins/Services/EventHandling/EventHook/RestAPI/src/connector.php [L]
```

### Plugin Installation

1. **Create directories:**
```bash
mkdir -p Customizing/global/plugins/Services/EventHandling/EventHook
cd Customizing/global/plugins/Services/EventHandling/EventHook
```

2. **Clone the repository:**
```bash
git clone --branch release_10 https://github.com/kroepelin-projekte/RestAPI.git RestAPI
```

3. **Install dependencies:**
```bash
cd RestAPI
composer install --no-dev
```

## Activation

1. Sign in to ILIAS with Administrator privileges.
2. Proceed to `Administration » Extending ILIAS » Plugins`
3. Locate the `RestAPI` plugin, then select `Actions » Install`, and subsequently, `Actions » Activate`.

## Usage

The API is accessed via the `/api/` prefix of your ILIAS installation.

### Authentication & Authorization

The API uses **HTTP Basic Authentication**. You must provide the credentials of a valid ILIAS user.

- **Header:** `Authorization: Basic <base64-encoded-credentials>`
- **Example (curl):**
  ```bash
  curl -H "Authorization: Basic $(echo -n 'username:password' | base64)" https://your-ilias.com/api/some/endpoint
  ```

#### Permission Configuration
Access is controlled via the Plugin Configuration in the ILIAS Administration:
1. Go to `Administration » Extending ILIAS » Plugins » RestAPI » Actions » Configure`.
2. **Step 1: API Permissions:** For each ILIAS role, you can choose:
   - **No Permission:** Role has no access to the API.
   - **Custom:** Role has access to specific components and methods (configured in Step 2).
   - **Full Permission:** Role has access to all endpoints and methods.
3. **Step 2: Role Permissions:** If "Custom" was selected, you can define the allowed HTTP methods (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`, etc.) for each component (e.g., `User`, `Course`) specifically for that role.
   - *Note:* For the `User` component, there is an additional `GET_EXISTS` permission to only allow checking if a user exists without fetching full data.

### API Calls

- **Base URL:** `https://your-ilias.com/api/`
- **Methods:** Supports `GET`, `POST`, `PUT`, `PATCH`, `DELETE` (depending on the specific endpoint).
- **Format:** Usually expects and returns `application/json`.

**Example request:**
`GET https://your-ilias.com/api/ilias/user/123`


## Documentation

The full REST API documentation (Swagger/OpenAPI or similar) is available directly within the ILIAS Administration:
1. Go to `Administration » Extending ILIAS » Plugins`
2. Find `RestAPI` -> `Actions » Configure`
3. Look for the "Documentation" tab.

---
This Open Source Plugin was developed by Kröpelin Projekt GmbH (https://www.kroepelin-projekte.de)
