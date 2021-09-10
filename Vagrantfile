# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure("2") do |config|
  config.vm.define "db" do |db|
    db.vm.box = "debian/bullseye64"
    db.vm.hostname = "db"
    db.vm.network :forwarded_port, guest: 22, host: 2201, protocol: "tcp"
    db.vm.provider :libvirt do |libvirt|
      libvirt.storage_pool_name = "default"
      libvirt.memory = 1024
    end
  end
  config.vm.define "ws" do |ws|
    ws.vm.box = "debian/bullseye64"
    ws.vm.hostname = "ws"
    ws.vm.network :forwarded_port, guest: 22, host: 2202, protocol: "tcp"
    ws.vm.provider :libvirt do |libvirt|
      libvirt.storage_pool_name = "default"
      libvirt.memory = 1024
    end
  end
  config.vm.define "ha" do |ha|
    ha.vm.box = "debian/bullseye64"
    ha.vm.hostname = "ha"
    ha.vm.network :forwarded_port, guest: 22, host: 2203, protocol: "tcp"
    ha.vm.network :forwarded_port, guest: 80, host: 80, protocol: "tcp"
    ha.vm.network :forwarded_port, guest: 443, host: 443, protocol: "tcp"
    ha.vm.provider :libvirt do |libvirt|
      libvirt.storage_pool_name = "default"
      libvirt.memory = 1024
    end
  end
end
