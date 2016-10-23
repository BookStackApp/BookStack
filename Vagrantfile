# -*- mode: ruby -*-
# vi: set ft=ruby :

$script = <<SCRIPT
# export http_proxy="http://proxy_host:proxy_port"
# export https_proxy="http://proxy_host:proxy_port"
wget https://raw.githubusercontent.com/BookStackApp/devops/master/scripts/installation-ubuntu-16.04.sh
chmod a+x installation-ubuntu-16.04.sh
./installation-ubuntu-16.04.sh
echo "BookStack available by url: http://localhost:8080"
SCRIPT

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.provider "virtualbox" do |vb|
    vb.cpus = 1
    vb.memory = 1024
    vb.name = 'bookstack'
  end
 
  config.vm.define :bookstack do |config|
    config.vm.box = "ubuntu/xenial64"
    config.vm.boot_timeout = 1800
    config.vm.provision "shell", inline: $script
    config.vm.network "forwarded_port", guest: 80, host: 8080
  end
end
