# HTTP Basic Authentication for TYPO3

* Supports TYPO3 9.5+

## Site Configuration

![site configuration](https://raw.githubusercontent.com/christophlehmann/httpbasicauth/master/Documentation/configuration.png)

## Webserver environment

You may need to add `SetEnvIf Authorization .+ HTTP_AUTHORIZATION=$0` to your `.htaccess` to make it work, see #6
