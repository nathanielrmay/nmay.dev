create table if not exists public.world
(
    pk                 bigint                                             not null
    primary key,
    name               varchar(100)                                       not null,
    created_date       timestamp with time zone default CURRENT_TIMESTAMP not null,
    last_played_date   timestamp with time zone,
                                     current_season     integer                  default 1                 not null,
                                     current_week       integer                  default 1                 not null,
                                     start_year         integer                                            not null,
                                     global_growth_rate numeric(4, 3)            default 1.000             not null,
    economy_scale      numeric(4, 3)            default 1.000             not null,
    randomness_factor  numeric(3, 2)            default 1.00              not null
    );

alter table public.world
    owner to postgres;

create table if not exists public.relationship
(
    entity1_pk        integer                                            not null,
    entity1_type      varchar(50)                                        not null,
    entity2_pk        integer                                            not null,
    entity2_type      varchar(50)                                        not null,
    relationship_type varchar(50)                                        not null,
    strength          integer                  default 0                 not null,
    created_date      timestamp with time zone default CURRENT_TIMESTAMP not null,
    updated_date      timestamp with time zone default CURRENT_TIMESTAMP not null,
                                    primary key (entity1_pk, entity1_type, entity2_pk, entity2_type, relationship_type)
    );

alter table public.relationship
    owner to postgres;

create table if not exists public."user"
(
    pk                 integer                                            not null
    primary key,
    username           varchar(50)                                        not null
    unique,
    email              varchar(255)                                       not null
    unique,
    password_hash      varchar(255)                                       not null,
    display_name       varchar(100),
    created_date       timestamp with time zone default CURRENT_TIMESTAMP not null,
    last_login_date    timestamp with time zone,
                                     is_active          boolean                  default true              not null,
                                     preferred_timezone varchar(50)              default 'UTC'::character varying
    );

alter table public."user"
    owner to postgres;

create table if not exists public.user_role
(
    pk          integer     not null
    primary key,
    name        varchar(50) not null
    unique,
    description text
    );

alter table public.user_role
    owner to postgres;

create table if not exists public.user_world_role
(
    user_id  integer not null
    references public."user"
    on delete cascade,
    world_id integer not null
    references public.world
    on delete cascade,
    role_id  integer not null
    references public.user_role,
    primary key (user_id, world_id)
    );

alter table public.user_world_role
    owner to postgres;

create table if not exists public.country
(
    pk                         integer                                                   not null
    primary key,
    world_pk                   integer                                                   not null
    references public.world
    on delete cascade,
    name                       varchar(100)                                              not null
    unique,
    code                       varchar(3)                                                not null
    unique,
    wealth_level               numeric(5, 2)            default 1.00                     not null,
    avg_temperature            integer                                                   not null,
    football_interest_modifier numeric(3, 2)            default 1.00                     not null,
    economic_stability         numeric(3, 2)            default 1.00                     not null,
    population                 integer,
    currency_code              varchar(3)               default 'USD'::character varying not null,
    growth_rate                numeric(4, 3)            default 1.000                    not null,
    created_date               timestamp with time zone default CURRENT_TIMESTAMP        not null
                                             );

alter table public.country
    owner to postgres;

create table if not exists public.state
(
    pk                          integer                                            not null
    primary key,
    country_pk                  integer                                            not null
    references public.country
    on delete cascade,
    name                        varchar(100)                                       not null,
    code                        varchar(10)                                        not null,
    wealth_modifier             numeric(3, 2)            default 1.00              not null,
    football_interest_modifier  numeric(3, 2)            default 1.00              not null,
    economic_stability_modifier numeric(3, 2)            default 1.00              not null,
    population                  integer,
    avg_temperature_modifier    integer                  default 0                 not null,
    tax_rate                    numeric(5, 2)            default 0.00              not null,
    growth_rate                 numeric(4, 3)            default 1.000             not null,
    created_date                timestamp with time zone default CURRENT_TIMESTAMP not null,
                                              unique (country_pk, name),
    unique (country_pk, code)
    );

alter table public.state
    owner to postgres;

create table if not exists public.city
(
    pk                          integer                                                      not null
    primary key,
    state_pk                    integer                                                      not null
    references public.state
    on delete cascade,
    name                        varchar(100)                                                 not null,
    wealth_modifier             numeric(3, 2)            default 1.00                        not null,
    football_interest_modifier  numeric(3, 2)            default 1.00                        not null,
    economic_stability_modifier numeric(3, 2)            default 1.00                        not null,
    population                  integer                                                      not null,
    avg_temperature_modifier    integer                  default 0                           not null,
    cost_of_living_modifier     numeric(3, 2)            default 1.00                        not null,
    growth_rate                 numeric(4, 3)            default 1.000                       not null,
    market_size                 varchar(20)              default 'medium'::character varying not null,
    created_date                timestamp with time zone default CURRENT_TIMESTAMP           not null,
                                              unique (state_pk, name)
    );

alter table public.city
    owner to postgres;

create table if not exists public.league
(
    pk                       integer                                                                  not null
    primary key,
    world_pk                 integer                                                                  not null
    references public.world
    on delete cascade,
    name                     varchar(100)                                                             not null,
    short_name               varchar(20)                                                              not null,
    prestige_rating          integer                  default 50                                      not null,
    is_professional          boolean                  default true                                    not null,
    salary_cap               numeric(12, 2),
    min_teams                integer                  default 8                                       not null,
    max_teams                integer                  default 32                                      not null,
    season_length            integer                  default 16                                      not null,
    playoff_format           varchar(50)              default 'single_elimination'::character varying not null,
    has_conferences          boolean                  default false                                   not null,
    has_divisions            boolean                  default false                                   not null,
    enforce_geographic_rules boolean                  default false                                   not null,
    created_date             timestamp with time zone default CURRENT_TIMESTAMP                       not null,
                                           is_active                boolean                  default true                                    not null
                                           );

alter table public.league
    owner to postgres;

