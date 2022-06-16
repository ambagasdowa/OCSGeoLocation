# OCSGeoLocation

OCS Inventory plugin Geolocation for windows Terminals

## Install

git clone https://github.com/ambagasdowa/OCSGeoLocation.git /usr/share/ocsinventory-reports/ocsreports/extensionsi/geolocation

### Plugin activation

Log into your Administration console and go to the "Extensions" tab.

Select the plugin and click on "Install".

![OCS plugin installation onglet](../../img/server/reports/plugin_installation_onglet.png)

![OCS plugin installation](../../img/server/reports/plugin_installation.png)

Logout and login to finish the web installation.

You can now proceed with the installation on your Communication server.

### Scripted communication server installation

To install your plugin on the Communication server, the `install_plugin.py` may be used.

On your Administration server, go to the `/usr/share/ocsinventory-reports/ocsreports/tools`
folder, and execute the script with sudo:

```bash
sudo python3 install_plugin.py
```

Enter the path to your Administration server's `extensions` folder:

```bash
Where is the plugins location [/usr/share/ocsinventory-reports/ocsreports/extensions/]
/path/to/plugin/
```

_Don't forget the last slash_

After, the script will present you all the plugins that can be installed.
Select the number of the plugin that you want to install:

```bash
[0] => plugin1
[1] => plugin2
[2] => plugin3
...
0
```

Now there are two specific cases:

- OCS Comuunication server is on the same server.
- OCS Communication server is on an other server.

  In the case where your Communication server is on an another server, you need to enter
  the server's informations:

  ```bash
  What is the host:
  127.0.0.1
  What is the username:
  root
  What is the password:
  Password:
  ```

  In both case, you will have to enter the path of your Communication server's configuration directory:

  ```bash
  Where is the server location [/etc/ocsinventory-server]

  ```

  The script will copy all the needed files into your Communication server's configuration directory.

  **`Note : Don't forget to restart your Communication server's apache service after the installation has finished`**
