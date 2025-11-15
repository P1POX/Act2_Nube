# Proyecto Docker: Aplicación Web PHP con MariaDB

Este es un proyecto de demostración que utiliza Docker Compose para orquestar una arquitectura de dos contenedores:

1.  **`web`**: Un servicio web construido con **PHP**
2.  **`db`**: Un servicio de base de datos **MariaDb 8.0**.

La aplicación web se conecta a la base de datos, realiza una consulta a una tabla llamada `clientes` y muestra los resultados en un navegador web.

---

## Características Principales

* **Orquestación con Docker Compose:** Definición de múltiples servicios, redes y volúmenes en un solo archivo `docker-compose.yml`.
* **Construcción de Imagen Personalizada:** El servicio `web` se construye a partir de un `Dockerfile` personalizado.
* **Base de Datos Persistente:** Los datos de MariaDb se guardan en un volumen de Docker, por lo que no se pierden al detener los contenedores.
* **Inicialización de Base de Datos:** Un script `init.sql` se ejecuta automáticamente la primera vez que se levanta la base de datos para crear la tabla `clientes` e insertar datos de ejemplo.
* **Control de Dependencias y Salud:** El servicio `web` utiliza `depends_on` y `condition: service_healthy` para esperar a que la base de datos `db` esté completamente lista antes de arrancar.
---

## Paso a Paso: Instalación y Ejecución

Sigue estos pasos para descargar y ejecutar el proyecto:

### 1. Clonar el Repositorio

Abre tu terminal y clona este repositorio en tu máquina local.

```bash
git clone https://github.com/P1POX/Act2_Nube
```
y ejecutar
```
docker compose up -d --build
```
