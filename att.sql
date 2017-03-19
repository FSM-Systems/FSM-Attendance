--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: attendance; Type: DATABASE; Schema: -; Owner: root
--

CREATE DATABASE attendance WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';


ALTER DATABASE attendance OWNER TO root;

\connect attendance

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: plpython2u; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpython2u WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpython2u; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpython2u IS 'PL/Python2U untrusted procedural language';


--
-- Name: plpythonu; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpythonu WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpythonu; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpythonu IS 'PL/PythonU untrusted procedural language';


--
-- Name: pltcl; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS pltcl WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION pltcl; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pltcl IS 'PL/Tcl procedural language';


--
-- Name: pltclu; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS pltclu WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION pltclu; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pltclu IS 'PL/TclU untrusted procedural language';


--
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry, geography, and raster spatial types and functions';


SET search_path = public, pg_catalog;

--
-- Name: ingeofence(numeric, numeric, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION ingeofence(numeric, numeric, integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$

declare 
geof record;
inside boolean;
lat alias for $1;
long alias for $2;
emp alias for $3;
begin
for geof in select ggeometry, gname from geofences where gid in (select aarea from employee_areas where aemp=emp) loop
	select into inside ST_Within(ST_GeomFromText('POINT(' || long || ' ' || lat ||')', 3857), geof.ggeometry) from geofences;
    if inside = true then
    	return geof.gname || '**' || lat || 'xxx' || long ;
    end if;
end loop;
return 'false' || '**' || lat || 'xxx' || long ;
end;

$_$;


ALTER FUNCTION public.ingeofence(numeric, numeric, integer) OWNER TO postgres;

--
-- Name: ingeofence(numeric, numeric, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION ingeofence(numeric, numeric, integer, text) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$

declare 
geof record;
inside boolean;
lat alias for $1;
long alias for $2;
emp alias for $3;
comm alias for $4;
begin
for geof in select ggeometry, gname from geofences where gid in (select aarea from employee_areas where aemp=emp) loop
	select into inside ST_Within(ST_GeomFromText('POINT(' || long || ' ' || lat ||')', 3857), geof.ggeometry) from geofences;
    if inside = true then
    	return geof.gname || '**' || lat || 'xxx' || long || '**' || coalesce(comm, '') ;
    end if;
end loop;
return 'false' || '**' || lat || 'xxx' || long || '**' || coalesce(comm, '') ;
end;

$_$;


ALTER FUNCTION public.ingeofence(numeric, numeric, integer, text) OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: emploee_attencance_bak; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE emploee_attencance_bak (
    eaid integer,
    eaemployee integer,
    eadate timestamp without time zone,
    ealatitude numeric(10,8),
    ealongitude numeric(10,8),
    eaaction character varying,
    eacomment text
);


ALTER TABLE emploee_attencance_bak OWNER TO root;

--
-- Name: employee_areas; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE employee_areas (
    aid integer NOT NULL,
    aemp integer,
    aarea integer
);


ALTER TABLE employee_areas OWNER TO postgres;

--
-- Name: employee_areas_aid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE employee_areas_aid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE employee_areas_aid_seq OWNER TO postgres;

--
-- Name: employee_areas_aid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE employee_areas_aid_seq OWNED BY employee_areas.aid;


--
-- Name: employee_attendance; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE employee_attendance (
    eaid integer NOT NULL,
    eaemployee integer,
    eadate timestamp without time zone,
    ealatitude numeric(10,8),
    ealongitude numeric(10,8),
    eaaction character varying,
    eacomment text
);


ALTER TABLE employee_attendance OWNER TO postgres;

--
-- Name: employee_attedance_eaid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE employee_attedance_eaid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE employee_attedance_eaid_seq OWNER TO postgres;

--
-- Name: employee_attedance_eaid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE employee_attedance_eaid_seq OWNED BY employee_attendance.eaid;


--
-- Name: employee_bak; Type: TABLE; Schema: public; Owner: root; Tablespace: 
--

CREATE TABLE employee_bak (
    eid integer,
    euuid character varying,
    ename character varying
);


ALTER TABLE employee_bak OWNER TO root;

--
-- Name: employees; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE employees (
    eid integer NOT NULL,
    euuid character varying,
    ename character varying
);


ALTER TABLE employees OWNER TO postgres;

--
-- Name: employees_eid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE employees_eid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE employees_eid_seq OWNER TO postgres;

--
-- Name: employees_eid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE employees_eid_seq OWNED BY employees.eid;


--
-- Name: geofences; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE geofences (
    gid integer NOT NULL,
    gshape text,
    gname character varying,
    gtype character varying,
    gradius numeric(20,10),
    gglobal boolean DEFAULT false,
    ggeometry geometry
);


ALTER TABLE geofences OWNER TO postgres;

--
-- Name: geofences_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE geofences_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE geofences_gid_seq OWNER TO postgres;

--
-- Name: geofences_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE geofences_gid_seq OWNED BY geofences.gid;


--
-- Name: aid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY employee_areas ALTER COLUMN aid SET DEFAULT nextval('employee_areas_aid_seq'::regclass);


--
-- Name: eaid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY employee_attendance ALTER COLUMN eaid SET DEFAULT nextval('employee_attedance_eaid_seq'::regclass);


--
-- Name: eid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY employees ALTER COLUMN eid SET DEFAULT nextval('employees_eid_seq'::regclass);


--
-- Name: gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY geofences ALTER COLUMN gid SET DEFAULT nextval('geofences_gid_seq'::regclass);


--
-- Name: employee_areas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY employee_areas
    ADD CONSTRAINT employee_areas_pkey PRIMARY KEY (aid);


--
-- Name: employee_attedance_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY employee_attendance
    ADD CONSTRAINT employee_attedance_pkey PRIMARY KEY (eaid);


--
-- Name: employees_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY employees
    ADD CONSTRAINT employees_pkey PRIMARY KEY (eid);


--
-- Name: geofences_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY geofences
    ADD CONSTRAINT geofences_pkey PRIMARY KEY (gid);


--
-- Name: employee_areas_aarea_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY employee_areas
    ADD CONSTRAINT employee_areas_aarea_fkey FOREIGN KEY (aarea) REFERENCES geofences(gid);


--
-- Name: employee_areas_aemp_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY employee_areas
    ADD CONSTRAINT employee_areas_aemp_fkey FOREIGN KEY (aemp) REFERENCES employees(eid);


--
-- Name: employee_attedance_eaemployee_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY employee_attendance
    ADD CONSTRAINT employee_attedance_eaemployee_fkey FOREIGN KEY (eaemployee) REFERENCES employees(eid);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

