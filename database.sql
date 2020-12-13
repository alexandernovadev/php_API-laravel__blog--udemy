CREATE DATABASE IF NOT EXISTS api_rest_laravel;

USE api_rest_laravel;

CREATE TABLE users(
    id              int(255) auto_increment not null,
    name            varchar(50) NOT NULL,
    surname         varchar(100),
    role            varchar(20),
    email           varchar(255) NOT null,
    password        varchar(255) NOT null,
    description     text,
    image           varchar(255),
    create_at       datetime DEFAULT null,
    update_at       datetime DEFAULT null,
    remember_token  varchar(255),

    CONSTRAINT pk_user PRIMARY KEY(id)
)ENGINE = InnoDb;


CREATE TABLE categories(
    id              int(255) auto_increment not null,
    name            varchar(100) NOT NULL,
    create_at       datetime DEFAULT null,
    update_at       datetime DEFAULT null,

    CONSTRAINT pk_user PRIMARY KEY(id)
)ENGINE = InnoDb;

CREATE TABLE posts(
    id              int(255) auto_increment not null,
    user_id         int(255) NOT NULL,
    category_id     int(100) not null,
    title           varchar(255) not null,
    content         text not null,
    image           varchar(255),
    create_at       datetime DEFAULT null,
    update_at       datetime DEFAULT null,

    CONSTRAINT pk_user PRIMARY KEY(id),
    CONSTRAINT fk_post_user FOREIGN KEY(user_id) REFERENCES users(id),
    CONSTRAINT fk_post_category FOREIGN KEY(category_id) REFERENCES categories(id)

)ENGINE = InnoDb;
