# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure("2") do |config|
  config.vm.define "db" do |db|
    db.vm.box = "debian/bullseye64"
    db.vm.hostname = "db"
    db.vm.synced_folder ".", "/vagrant", type: "rsync"
    db.vm.network :forwarded_port, guest: 22, host: 2201, protocol: "tcp"
    db.vm.provider :libvirt do |libvirt|
      libvirt.storage_pool_name = "default"
      libvirt.memory = 1024
    end
  end
  config.vm.define "ws" do |ws|
    ws.vm.box = "debian/bullseye64"
    ws.vm.hostname = "ws"
    ws.vm.synced_folder ".", "/vagrant", type: "rsync"
    ws.vm.network :forwarded_port, guest: 22, host: 2202, protocol: "tcp"
    ws.vm.network :forwarded_port, guest: 80, host: 80, protocol: "tcp", host_ip: "0.0.0.0"
    ws.vm.network :forwarded_port, guest: 443, host: 443, protocol: "tcp", host_ip: "0.0.0.0"
    ws.vm.provider :libvirt do |libvirt|
      libvirt.storage_pool_name = "default"
      libvirt.cpus = 2
      libvirt.memory = 2048
    end
  end
end
