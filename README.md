# Cyberpanel WHMCS Module

## Installation

You can start by installing CyberPanel on your server using the following command as root user and following the instructions:

```bash
sh <(curl https://cyberpanel.net/install.sh || wget -O - https://cyberpanel.net/install.sh)
```

Then, you need to copy the `modules` folder to your WHMCS installation directory and then:

1. Go to your WHMCS admin panel.
2. Go to `Setup` -> `Products/Services` -> `Servers`.
3. Click on `Add New Server`.
4. Fill in the details:
   - Name: `CyberPanel`
   - Hostname: `https://your-cyberpanel-server.com:8090`
   - IP Address: `your-cyberpanel-server-ip`
   - Type: `CyberPanel`
   - Username: `admin`
   - Password: `your-cyberpanel-password`
   - Access Hash: `your-cyberpanel-access-hash`
   - Secure: `Checked/Unchecked` (Depends on your server configuration)
   - Test Connection: `Checked`
   - Click `Save Changes`.
   - Click `Close`.
5. Go to `Setup` -> `Products/Services` -> `Products/Services`.
6. Click on `Create a New Group`.
7. Fill in the details:
   - Name: `CyberPanel`
   - Click `Save Changes`.
   - Click `Close`.
   - Click on `Create a New Product`.
   - Fill in the details:
   - Product Type: `Hosting Account`
   - Product Group: `CyberPanel`
   - Product Name: `CyberPanel Shared Hosting`
   - Description: `CyberPanel Shared Hosting`
   - Click `Continue`.
   - Fill in the details:
   - Module Settings:
   - Module Name: `CyberPanel`
   - Package Name: `Default` (You can create a package in CyberPanel and use it here)
   - ACL: `user/admin` (This will allow you to login as a user or admin)
   - Click `Save Changes`.
   - Click `Close`.

## Usage

1. Go to your WHMCS client area.
2. Go to `Services` -> `My Services`.
3. Click on the product you created.
4. Click on `Login to CyberPanel`.
5. You will be redirected to your CyberPanel dashboard.
6. You can manage your hosting account from there.

## Supported Operations

- Create Account
- Suspend Account
- Unsuspend Account
- Terminate Account
- Change Package
- Change Password
- Test Connection
- Login to CyberPanel (User, Admin)
