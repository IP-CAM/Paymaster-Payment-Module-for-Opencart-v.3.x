# PayMaster for OpenCart 3.x

## Creating the extension (module)

1. Open *install.xml* and fill in the following fields:
    * link (PayMaster website URL);
    * base_service_url (PayMaster API base URL);
    * display_service_name (payment method name that will be shown to user).

    Filling example:

    > &lt;link&gt;https://paymaster24.com&lt;/link&gt;  
    > &lt;base_service_url&gt;https://psp.paymaster24.com&lt;/base_service_url&gt;  
    > &lt;display_service_name&gt;PayMaster (bank cards, electronic money and more)&lt;/display_service_name&gt;

2. Zip the resulting *install.xml* and *upload* folder. Name the archive file **paymaster.ocmod.zip**.

## Installing the extension (module)

Please read the [user guide](user-guide.pdf).
