## Quick Reference

BookStack is installed by default at `/var/www/bookstack/`

Editing the .env file: `nano /var/www/bookstack/.env`

Apache
- Check status: `sudo systemctl status apache2`
- Check logs: `nano /var/log/apache2/<access/error/other_vhosts_access>.log`
- Restart: `sudo service apache2 restart`

## [Updating Bookstack](https://www.bookstackapp.com/docs/admin/updates/)

Make the latest repo ran `npm run production` before committing and pushing to capture the CSS and JS production files. (I really should have a production branch that handles this automatically)

`cd /var/www/bookstack/`

`git pull origin`

`composer install --no-dev` (I think this will actually clear the artisan cache if run with sudo privileges, but I'd rather not run with sudo)

`php artisan migrate`

And optionally,

`sudo php artisan cache:clear`

`sudo php artisan config:clear`

`sudo php artisan view:clear`

All together: `cd /var/www/bookstack/ && git pull origin && composer install --no-dev && php artisan migrate && sudo php artisan cache:clear && sudo php artisan config:clear && sudo php artisan view:clear`


## Fresh install

Same instructions as the [Ubuntu 22.04 Installation Script](https://www.bookstackapp.com/docs/admin/installation/#ubuntu-2204-installation-script) with the URL and file names changed
```
wget https://raw.githubusercontent.com/CaffeineSheep/BookStack/development/install_scripts/install-script-ubuntu-22.04.sh
chmod a+x install-script-ubuntu-22.04.sh
sudo ./install-script-ubuntu-22.04.sh
```

## Bookstack

#### Logs: `/var/www/bookstack/storage/logs/laravel.log`

## EC2 Notes

Connecting with terminal: Go to the EC2 Instance Summary, then press "Connect" button (Under SSH client) to get SSH details, then use `ssh -i <keypair>.cer ubuntu@<ec2-public-dns>`

Copy file local to remote: `sudo scp -i <keypair path>.cer <local-file-to-copy> ubuntu@<ec2-public-dns>:/var/www/bookstack/public`

(EC2 public DNS looks like `ec2-xx-xx-xx-xx.us-xx-x.compute.amazonaws.com`)

## Misc

Remember that PHP commands need to be done from the BookStack installation directory, so `cd /var/www/bookstack/` first


ldapsearch -H ldap://ldap.symbolpedia.tk -b "dc=symbolpedia,dc=tk" -D "uid=admin,ou=Users,dc=symbolpedia,dc=tk" -W


ldapsearch -x -b "dc=symbolpedia,dc=tk" -H ldap://ldap.symbolpedia.tk -D "uid=admin,ou=Users,dc=symbolpedia,dc=tk" -W

`ldapsearch -x -b "dc=symbolpedia,dc=tk" -H ldap://ldap.symbolpedia.tk -D "uid=<username>,ou=Users,dc=symbolpedia,dc=tk" -W` then enter <username>'s password

Info about specific user `ldapsearch -xLLL -H ldap://ldap.symbolpedia.tk -b "dc=symbolpedia,dc=tk" uid=<username> \* +` (https://serverfault.com/questions/132026/listing-group-members-using-ldapsearch)

https://kifarunix.com/how-to-create-openldap-member-groups/

https://www.linuxtopia.org/online_books/network_administration_guides/ldap_administration/overlays_Reverse_Group_Membership_Maintenance.html