# Grup4-Alfred_Emilio_Salma

### Integrants
- Alfred Perez

- Emilio Fernandez

- Salma Picazo


### Links
[Trello](https://trello.com/b/6rF5Ppzh/projecte-slides-grup-4)

[Figma](https://www.figma.com/file/aAWb0YlNiNHMsdyzinLiPz/Home?type=design&node-id=0-1&mode=design&t=lb3MXD1uq1Mr0XzS-0)


### Portafolis
[Salma](https://github.com/Salmaa258)

[Emilio]()

[Alfred]()

## CONFIGURACIÓN SERVIDOR:

- Desplegada en la máquina de Salma (192.168.50.159)

#### Instalación:

sudo apt install default-mysql-server


sudo apt install git


git clone https://git.copernic.cat/daw2/grup4-alfred_emilio_salma.git -b develop

#### Configuración ficheros:

sudo nano /etc/php/8.2/apache2/php.ini

- display_errors= On

- display_startup_errors= On

- extension=pdo_mysql : quitarle el comentario


sudo systemctl restart apache2


sudo nano /etc/network/interfaces

auto enp2s0
auto enp3s0
allow-hotplug enp1s0
iface enp1s0 inet dhcp
iface enp2s0 inet dhcp
iface enp3s0 inet static
        address 192.168.50.159
        netmask 255.255.255.0
        
        
sudo mv grup4-alfred_emilio_salma/ /var/www/html/


sudo nano /var/www/html/grup4_alfred_emilio_salma/config.php

cambiar linia -> define('DB_HOST', '127.0.0.1:3306'); 

#### Creación BBDD:

sudo mysql
> CREATE DATABASE db_Presentaciones;

> use db_Presentaciones;

> CREATE USER 'usuari'@'%' IDENTIFIED BY 'password1';

> GRANT ALL PRIVILEGES ON db_Presentaciones.* TO 'usuari'@'%';


mysql -u usuari -p

> insert into presentacion values(null,'aaa','aaa');

        
