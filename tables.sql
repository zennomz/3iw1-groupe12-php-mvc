DROP TABLE IF EXISTS "page";
DROP TABLE IF EXISTS "user" CASCADE;

CREATE TABLE public."user" (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(320) NOT NULL,
    pwd VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    date_created DATE NOT NULL DEFAULT CURRENT_DATE,
    date_updated DATE,
    verification_token VARCHAR(64)
);

ALTER TABLE "user" OWNER TO devuser;

CREATE TABLE "page" (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    author_id INTEGER REFERENCES "user"(id),
    date_created DATE NOT NULL DEFAULT CURRENT_DATE,
    date_updated DATE,
    slug VARCHAR(100) UNIQUE NOT NULL
);

ALTER TABLE "page" OWNER TO devuser;
