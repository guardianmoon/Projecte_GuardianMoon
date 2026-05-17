# Projecte GuardianMoon



# GuardianMoon: Sistema de Seguretat Personal IoT

GuardianMoon és una solució de seguretat integral que connecta dispositius *wearables*, automatització mòbil i una infraestructura de servidor centralitzada per oferir assistència immediata en situacions d'emergència.

## 1. Descripció del Projecte
El projecte neix amb l'objectiu de minimitzar el temps de resposta en situacions de risc (agressions, accidents, emergències mèdiques). Mitjançant un polsador físic discret, l'usuari pot enviar la seva ubicació exacta i l'estat del seu dispositiu a un Dashboard de monitoratge sense necessitat d'interactuar directament amb el telèfon mòbil.

## 2. Arquitectura del Sistema
El sistema es basa en un flux de dades circular dividit en tres capes:

------- Capa de Hardware (IoT)-------------------
* **Dispositiu:** Polsador Bluetooth Low Energy (BLE) integrat en joieria o accessoris.
* **Protocol:** BLE 5.3 per a un consum mínim de bateria i connexió instantània.

------- Capa de Comunicació (Mòbil)--------------
* **Automatització:** Ús de MacroDroid (Android) o Shortcuts (iOS).
* **Lògica:** En detectar la connexió del botó, el mòbil recull les coordenades GPS i el nivell de bateria.
* **Transmissió:** Enviament de dades mitjançant peticions HTTP POST encriptades cap al servidor.

------- Capa d'Infraestructura (Servidor)---------
* **Contenidors:** Implementació basada en Docker per garantir la portabilitat.
* **Seguretat:** Proxy invers amb Nginx i xifrat de dades SSL.
* **Integració ERP/CRM (Odoo):** Connexió amb Odoo per a la gestió de clients, inventari de dispositius i generació automàtica de tiquets de suport en cas d'alerta crítica.

## 3. Stack Tecnològic
| Àmbit | Tecnologia |
| :--- | :--- |
| **Infraestructura** | Docker, Nginx (Proxy Revers) |
| **Gestió CRM/ERP** | Odoo |
| **Base de Dades** | MariaDB |
| **Backend** | PHP 8.4 |
| **Frontend** | HTML5, CSS3 (Custom Retro Design), JavaScript |
| **Automatització** | MacroDroid / iOS Shortcuts |



## 4. Funcionalitats Principals
- **Monitoratge en Temps Real:** Dashboard amb mapa interactiu que se centra en l'última alerta.
- **Gestió de Suport (Odoo):** Integració de fluxos de treball on cada alerta pot derivar en un tiquet d'incidència oficial.
- **Filtrat Multi-usuari:** Sistema de privacitat basat en sessions on cada client només veu el seu dispositiu.
- **Historial d'Incidències:** Registre auditable de totes les alertes enviades i gestionades.
  
## 5. Equip de Desenvolupament
* **Mario:** Hardware & IoT Architect (Disseny del polsador i integració BLE).
* **Oussama:** Web & Interface Lead (Desenvolupament Full-Stack i UX/UI).
* **Issam:** Infrastructure & Security (Dockerización, xarxes i seguretat de servidor).

## 6. Requisits per a la Instal·lació
Per desplegar el codi font d'aquest repositori, es requereix:
1. Un servidor web compatible amb **PHP 8.0+**.
2. Un servidor de base de dades **MariaDB/MySQL**.
3. Importar l'estructura SQL proporcionada per a les taules `clientes` i `alertes`.
4. Configurar el fitxer `conexion.php` amb les credencials de la base de dades.

## 7. Desplegament de la Infraestructura (Docker Swarm)
El fitxer docker-compose.yml inclòs està optimitzat per treballar en Alta Disponibilitat (HA) replicant els serveis crítics en un clúster. Segueix aquests passos per fer el desplegament:

- **Pas 1: Configurar les Variables d'Entorn**
  Atès que el fitxer d'orquestració utilitza variables protegides per seguretat, crea un fitxer anomenat .env a la mateixa carpeta on es troba el docker-compose.yml i defineix les teves claus:

  Fragmento de código
  DB_PASSWORD=la_teva_contrasenya_usuari_db
  DB_ROOT_PASSWORD=la_teva_contrasenya_root_db
  POSTGRES_USER=odoo
  POSTGRES_PASSWORD=la_teva_contrasenya_postgres
- **Pas 2: Inicialitzar el mode Swarm**
  Si el teu entorn encara no opera en mode clúster, executa la inicialització al node principal (Manager):

  Bash
  docker swarm init
- **Pas 3: Llançar el Stack de Serveis**
  Per aixecar tota l'infraestructura replicada (Nginx Proxy Manager, Aplicació Web, MariaDB, Postgres, Odoo i Adminer) sota el rang de xarxes overlay internes definides, executa:

  Bash
  docker stack deploy -c docker-compose.yml guardianmoon
- **Pas 4: Verificar l'estat dels Microserveis**
  Pots verificar que els serveis i les seves rèpliques estan totalment operatius (2/2 en els serveis balancejats) mitjançant la comanda:

  Bash
  docker service ls

© 2026 GuardianMoon Team - Tecnologia que salva vides.
