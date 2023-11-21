$install_requirements = <<SCRIPT
echo ">>> Installing Base Requirements"

#!/usr/bin/env bash

DBHOST=localhost
DBNAME=db_Presentaciones
ROOTPASSWD=root

# Usuario y contraseña
USER="usuari"
PASS="password1"

sudo apt update
sudo apt install -y vim curl build-essential python3-software-properties git

sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $ROOTPASSWD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $ROOTPASSWD"

# Instalar MySQL
sudo apt -y install mysql-server

sudo mysql -uroot -p$ROOTPASSWD -e "CREATE DATABASE $DBNAME"

# Crear un usuario y otorgar permisos
sudo mysql -uroot -p$ROOTPASSWD -e "CREATE USER '$USER'@'%' IDENTIFIED BY '$PASS';"
sudo mysql -uroot -p$ROOTPASSWD -e "GRANT ALL PRIVILEGES ON $DBNAME.* TO '$USER'@'%';"

# Actualizar el archivo de configuración de MySQL para permitir acceso remoto a la base de datos
sudo sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mysql.conf.d/mysqld.cnf

sudo systemctl restart mysql

# Crear tablas en la base de datos
sudo mysql -uroot -p$ROOTPASSWD $DBNAME <<EOF

CREATE TABLE tema(
  id  VARCHAR(10),

  PRIMARY KEY(id)
);

INSERT INTO tema VALUES('oscuro');
INSERT INTO tema VALUES('claro');

CREATE TABLE presentacion(
  id              INT      AUTO_INCREMENT,
  titulo          VARCHAR(255),
  descripcion     VARCHAR(255),
  tema            VARCHAR(10),
  url             CHAR(10),
  pin             VARCHAR(8) DEFAULT 'null',

  PRIMARY KEY(id),
  FOREIGN KEY (tema) 
    REFERENCES tema(id)
);

CREATE TABLE diapositiva(
  id                INT AUTO_INCREMENT,
  presentacion_id   INT,
  orden             INT,

  PRIMARY KEY(id, presentacion_id),
  FOREIGN KEY (presentacion_id) 
    REFERENCES presentacion(id) 
    ON DELETE CASCADE
);

CREATE TABLE tipoTitulo(
  diapositiva_id    INT,
  presentacion_id   INT,
  titulo            VARCHAR(255),

  PRIMARY KEY (diapositiva_id, presentacion_id),
  FOREIGN KEY (diapositiva_id) 
    REFERENCES diapositiva(id) 
    ON DELETE CASCADE,
  FOREIGN KEY (presentacion_id) 
    REFERENCES presentacion(id) 
    ON DELETE CASCADE
);

CREATE TABLE tipoContenido(
  diapositiva_id  INT,
  presentacion_id INT,
  titulo          VARCHAR(255),
  contenido       VARCHAR(1280),

  PRIMARY KEY(diapositiva_id, presentacion_id),
  FOREIGN KEY (diapositiva_id) 
    REFERENCES diapositiva(id)
    ON DELETE CASCADE,
  FOREIGN KEY (presentacion_id) 
    REFERENCES presentacion(id)
    ON DELETE CASCADE
);

CREATE TABLE tipoImagen(
  diapositiva_id  INT,
  presentacion_id INT,
  titulo          VARCHAR(255),
  contenido       VARCHAR(1280),
  nombre_imagen   VARCHAR(255),

  PRIMARY KEY(diapositiva_id, presentacion_id),
  FOREIGN KEY (diapositiva_id) 
    REFERENCES diapositiva(id)
    ON DELETE CASCADE,
  FOREIGN KEY (presentacion_id) 
    REFERENCES presentacion(id)
    ON DELETE CASCADE
);

CREATE TABLE tipoPregunta(
  diapositiva_id  INT,
  presentacion_id INT,
  titulo          VARCHAR(255),
  pregunta        VARCHAR(255),
  respuesta_a     VARCHAR(255),
  respuesta_b     VARCHAR(255),
  respuesta_c     VARCHAR(255),
  respuesta_d     VARCHAR(255),
  respuesta_correcta VARCHAR(255),

  PRIMARY KEY(diapositiva_id, presentacion_id),
  FOREIGN KEY (diapositiva_id) 
    REFERENCES diapositiva(id)
    ON DELETE CASCADE,
  FOREIGN KEY (presentacion_id) 
    REFERENCES presentacion(id)
    ON DELETE CASCADE
);

EOF
SCRIPT

Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu/mantic64"

  config.vm.provider "virtualbox" do |vb| 
	  vb.name = "Slides DB" 
    vb.memory = "1024" 
    vb.cpus = 2 
  end

  config.vm.define "db-server" do |db|
      db.vm.network "private_network", ip: "192.168.56.1"
      db.vm.network "forwarded_port", guest: 3306, host: 8808
      db.vm.network "forwarded_port", guest: 80, host: 8306
      db.vm.provision "shell", inline: $install_requirements
  end
end
