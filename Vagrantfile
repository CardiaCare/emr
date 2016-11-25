# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = 'ubuntu/xenial64'
  config.vm.network "forwarded_port", guest: 80, host: 8089
  config.vm.hostname = "emr.local"
  config.vm.synced_folder '.', '/var/www/emr'
  config.vm.provision 'shell', :path => "bootstrap.sh"

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
  end

end
