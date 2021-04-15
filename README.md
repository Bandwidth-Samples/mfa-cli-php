# 2FA-CLI-PHP
<a href="http://dev.bandwidth.com"><img src="https://s3.amazonaws.com/bwdemos/BW-VMP.png"/></a>
</div>

 # Table of Contents

<!-- TOC -->

- [2FA-CLI-PHP](#2FA-CLI-PHP)
- [Description](#description)
- [Bandwidth](#bandwidth)
- [Environmental Variables](#environmental-variables)
- [Callback URLs](#callback-urls)
    - [Ngrok](#ngrok)

<!-- /TOC -->

# Description
A PHP CLI application that allows you to test the Bandwidth 2 Factor Authentication API by sending a code to a PSTN number via phone call or SMS, entering that code into the CLI, and returning `TRUE` or `FALSE`. 

# Bandwidth
In order to use the Bandwidth API users need to set up the appropriate application at the [Bandwidth Dashboard](https://dashboard.bandwidth.com/) and create API tokens.

To create an application log into the [Bandwidth Dashboard](https://dashboard.bandwidth.com/) and navigate to the `Applications` tab.  Fill out the **New Application** form selecting the service (Messaging or Voice) that the application will be used for.  All Bandwidth services require publicly accessible Callback URLs, for more information on how to set one up see [Callback URLs](#callback-urls).

For more information about API credentials see [here](https://dev.bandwidth.com/guides/accountCredentials.html#top)

# Environmental Variables
The sample app uses the below environmental variables.
```php
BW_ACCOUNT_ID                 // Your Bandwidth Account Id
BW_USERNAME                   // Your Bandwidth API Token
BW_PASSWORD                   // Your Bandwidth API Secret
BW_PHONE_NUMBER               // Your The Bandwidth Phone Number
BW_VOICE_APPLICATION_ID       // Your Voice Application Id created in the dashboard
BW_MESSAGING_APPLICATION_ID   // Your Messaging Application Id created in the dashboard
```
