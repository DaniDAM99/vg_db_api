# vg_db_api
API hecha con Symfony y JWT

## Instalación

### Instalar Symfony y Composer
PAra poder ejecutar el proyecto hay que instalar Symfony y Composer<br>
Symfony: https://symfony.com/download<br>
Composer: https://getcomposer.org/download/

### Clonar el proyecto

Accedes a la ruta donde quieras clonarlo
```bash
cd ruta
```
y clonamos el proyecto
```bash
git clone https://github.com/DaniDAM99/vg_db_api
```

### Instalar dependencias
Accedemos a la carpeta del proyecto 
```bash
cd ruta/vg_db_api
```
y ejecutamos el comando para instalar las dependencias
```bash
composer install
```

### Arrancamos el servidor
Dentro de la carpeta del proyecto ejecutamos el siguiente comando
```bash
symfony server:start
```

### Rutas Usuario

| Método | Ruta | Acción |
| :---: | --- | --- |
| **GET** |localhost:8000/usuarios| Obtenenmos el usuario logueado |
| **POST** | localhost:8000/usuarios/registrar | Registrar un usuario nuevo |
| **POST** | localhost:8000/usuarios/login | Login con los datos recibidos |
| **PUT** | localhost:8000/usuarios | Modifica los datos de usuario por los datos recibidos |
| **DELETE** | localhost:8000/usuarios | Elimina el usuario logueado |

### Rutas Lista

| Método | Ruta | Acción |
| :---: | --- | --- |
| **GET** |localhost:8000/listas| Obtenenmos las listas del usuario logueado |
| **GET** |localhost:8000/listas/ID| Obtenenmos la lista con id ID del usuario logueado |
| **POST** | localhost:8000/listas | Crea una lista con los datos recibidos |
| **PUT** | localhost:8000/listas/ID | Modifica los datos de la lista con id ID por los datos recibidos |
| **DELETE** | localhost:8000/listas/ID | Elimina la lista con id ID |

### Rutas Juego

| Método | Ruta | Acción |
| :---: | --- | --- |
| **GET** |localhost:8000/juegos| Obtiene todos juegos |
| **GET** |localhost:8000/juegos/ID| Obtiene el juego con id ID |
| **POST** | localhost:8000/juegos | Añade un juego con los datos recibidos |
| **PUT** | localhost:8000/juegos/ID | Modifica los datos del juego con id ID por los datos recibidos |
| **PUT** | localhost:8000/juegos/add/IDJUEGO/IDLISTA | Añade el juego con id IDJUEGO a la lista con el id IDLISTA |
| **DELETE** | localhost:8000/juegos/borrar/IDJUEGO/IDLISTA | Elimina el juego con id IDJUEGO de la lista con el id IDLISTA |
| **DELETE** | localhost:8000/juegos/ID | Elimina el juego con id ID |
