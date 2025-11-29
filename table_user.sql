-- Table: public.user

-- DROP TABLE IF EXISTS public."user";

DROP TABLE IF EXISTS "user";

CREATE TABLE "user" (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(320) NOT NULL,
    pwd VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    date_created DATE NOT NULL DEFAULT CURRENT_DATE,
    date_updated DATE
);

ALTER TABLE "user" OWNER TO devuser;
