# Rest API - ILIAS Plugin

**Table of Contents**

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Compatibility](#compatibility)
- [Installation](#installation)
- [Activation](#activation)
- [Usage](#usage)

## Introduction

This plugin enables an interface access in the REST paradigm.

## Requirements
 
Composer

### Compatibility
| Plugin Branches | ILIAS Versions | PHP Versions |
|-----------------|----------------|--------------|
| release_9       | 9              | 8.1 - 8.2    |
| release_10      | 10             | 8.2 - 8.3    |


## Installation

1. Got to `/etc/apache2/sites-enabled`
2. Open in vim or nano `000-default.conf`
3. Create in Virtual Host
```apacheconf
 <Directory /var/www/html>
        RewriteEngine On
        AllowOverride None
       	RewriteCond %{HTTP:Authorization} ^(.*)
        RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]

        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-l
        RewriteRule ^/?api/(.*) /public/Customizing/global/plugins/Services/EventHandling/EventHook/RestAPI/src/connector.php [L]
 </Directory>
```
4. Save and Exit
5. Reload or Restart Apache
6. Alternatively, it can also be added to the .htaccess file.

**Create directories**
```bash
mkdir -p Customizing/global/plugins/Services/EventHandling/EventHook
cd Customizing/global/plugins/Services/EventHandling/EventHook
```

**Clone with SSH**
```bash
git clone git@github.com:kroepelin-projekte/RestAPI.git RestAPI
```

**Or clone with HTTPS**
```bash
git clone https://github.com/kroepelin-projekte/RestAPI.git RestAPI
```

**Install dependencies**
```bash
composer install --no-dev
```

**ILIAS Composer**
```bash
composer composer in root
```

## Activation
1. Sign in to ILIAS with Administrator privileges.
2. Proceed to `Administration » Extending ILIAS » Plugins`
3. Locate the desired plugin, then select `Actions » Install`, and subsequently, `Actions » Activate`.

## Usage
- URL ist <URL>/api/..
- Methods: GET, POST, PUT, DELETE
- Header: Authorization: Basic
- Check the documentation for the routes.

This Open Source Plugin was developed by Kröpelin Projekt GmbH (https://www.kroepelin-projekte.de)