# PayMaster for OpenCart 3.x

## Creating the extension (module)

1. Open *install.xml* and fill in the following fields:
    * link (PayMaster website URL);
    * base_service_url (PayMaster API base URL);
    * display_service_name (payment method name that will be shown to user).

    Filling example:

    ```xml
    <link>https://paymaster24.com</link>
    <base_service_url>https://psp.paymaster24.com</base_service_url>
    <display_service_name>PayMaster (bank cards, electronic money and more)</display_service_name>
    ```

2. Zip the resulting *install.xml* and *upload* folder. Name the archive file **paymaster.ocmod.zip**.

## Installing the extension (module)

Please read the [user guide](user-guide.pdf).
