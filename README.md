# 🐘 Laravel Book Registry

## Contents:

🎯 Objective

🏃 Running the project

📄 Scripts

🔍 Acessing the Database

📚 API Documentation

🚧 Troubleshooting

---

## 🎯 Objective

Create an RESTful API to register books and indices/summary. The logged user that registers a book will be it's publisher.

**Requirements**

- The API should be made with PHP, using Laravel;
- It should persist data in PostgreSQL or MySQL;
- Create book register routes following the instructions below;
- Create unit tests in each operation;
- All routes should use authentication;
- It should be published at GitHub.

For more details, check the **📚 API Documentation** section.

## 🏃 Running the project

I **highly recommend** having a **Docker** environment with support to **Compose V2**'s `docker compose` [**CLI plugin**](https://docs.docker.com/compose/install/linux/).

_This project uses bash scripts to make some commands easier to run and was tested only on a Linux machine. If you are using Windows, I highly recommend you running this project inside a WSL2 distro, or using Git Bash as your terminal._

Open your terminal in the root folder and type:

```sh
./run.sh
```

This script will make sure to build your images, install all dependencies (if you doesn't have a `vendor` folder yet) and run all migrations. In subsequent runs, it will skip the installation step and directly start all containers.

All migrations will be applied, the API will be served and the queue worker will start in parallel.

To stop running containers, just type

```sh
./stop.sh
```

and all your containers will be dropped.

## 📄 Scripts

Beyond `run.sh` and `stop.sh`, we have another helper script:

- `build.sh`: Rebuilds the images in case you changed something in the Dockerfile.

## 🔍 Acessing the Database

You can access the database using your favorite Database Administration Tool. The PostgreSQL database is open and exposed at port 5432.

If you don't have one tool or you don't want to configure new connections, you can use _Adminer_ in your browser, available at `http://localhost:8080/`.

**Credentials**

- System: `PostgreSQL`
- Server: `postgresql:5432` (using inner-network names)
- User: `user`
- Password: `pass`
- Database: `database`

## 📚 API Documentation

### `POST` v1/auth/token

Retrieve access token.

> ⚠️ There are only two users available for authentication:
>
> - Email: autor@neves.com; Password: 12345678
> - Email: fulana@couves.com; Password: 87654321

### `GET` v1/livros

Search books.

### `POST` v1/livros

Register book.

### `POST` v1/livros/{livroId}/importar-indices-xml

Schedule a job to export a book summary to a XML file.

## 🚧 Troubleshooting

> Hint: This section covers only troubleshooting with Docker containers!

- Make sure you have these ports available before running the projects:
  - `5432`: PostgreSQL
  - `8000`: API
  - `8080`: Adminer
- Make sure your Docker daemon is running!
- Make sure you are using a newer version of Docker that supports Docker Compose official [**CLI plugin**](https://docs.docker.com/compose/install/linux/)! **This project does not use `docker-compose`**. This format will no longer be supported from the end of June 2023, according to the docs.
- If you are somehow receiving `Permission denied` when trying to run any scripts, run
  ```sh
  chmod +x ./*.sh && chmod +x ./docker/*.sh
  ```
  to make sure your terminal can execute utility scripts and docker entrypoint scripts.
