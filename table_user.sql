-- Table: public.user

-- DROP TABLE IF EXISTS public."user";

CREATE TABLE IF NOT EXISTS public."user"
(
    id integer NOT NULL DEFAULT nextval('user_id_seq'::regclass),
    username character varying(50) COLLATE pg_catalog."default",
    email character varying(320) COLLATE pg_catalog."default" NOT NULL,
    pwd character varying(255) COLLATE pg_catalog."default" NOT NULL,
    is_active boolean DEFAULT false,
    date_created date NOT NULL DEFAULT CURRENT_DATE,
    date_updated date,
    CONSTRAINT user_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

-- Ajouter la colonne verification_token si elle n'existe pas déjà (pour les migrations)
ALTER TABLE IF EXISTS public."user" 
    ADD COLUMN IF NOT EXISTS verification_token character varying(64) COLLATE pg_catalog."default";

ALTER TABLE IF EXISTS public."user"
    OWNER to devuser;