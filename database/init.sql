--
-- PostgreSQL database dump
--

-- Dumped from database version 14.17 (Homebrew)
-- Dumped by pg_dump version 14.17 (Homebrew)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: update_timestamp(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.update_timestamp() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_timestamp() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: bookings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bookings (
    id integer NOT NULL,
    user_id integer NOT NULL,
    tour_id integer NOT NULL,
    booking_date timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    guests integer NOT NULL,
    total_price numeric(10,2) NOT NULL,
    status character varying(20) DEFAULT 'confirmed'::character varying,
    payment_method character varying(50),
    notes text
);


ALTER TABLE public.bookings OWNER TO postgres;

--
-- Name: bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.bookings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.bookings_id_seq OWNER TO postgres;

--
-- Name: bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.bookings_id_seq OWNED BY public.bookings.id;


--
-- Name: favorites; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.favorites (
    id integer NOT NULL,
    user_id integer NOT NULL,
    tour_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.favorites OWNER TO postgres;

--
-- Name: favorites_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.favorites_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.favorites_id_seq OWNER TO postgres;

--
-- Name: favorites_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.favorites_id_seq OWNED BY public.favorites.id;


--
-- Name: tours; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tours (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    short_description text,
    description text NOT NULL,
    price numeric(10,2) NOT NULL,
    location character varying(255) NOT NULL,
    type character varying(50) NOT NULL,
    date date NOT NULL,
    image character varying(255),
    featured boolean DEFAULT false,
    active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.tours OWNER TO postgres;

--
-- Name: tours_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tours_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tours_id_seq OWNER TO postgres;

--
-- Name: tours_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tours_id_seq OWNED BY public.tours.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    email character varying(100) NOT NULL,
    password text NOT NULL,
    phone character varying(20),
    birth_date date,
    reset_token text,
    reset_expires timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    last_login timestamp without time zone
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: bookings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings ALTER COLUMN id SET DEFAULT nextval('public.bookings_id_seq'::regclass);


--
-- Name: favorites id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favorites ALTER COLUMN id SET DEFAULT nextval('public.favorites_id_seq'::regclass);


--
-- Name: tours id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tours ALTER COLUMN id SET DEFAULT nextval('public.tours_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: bookings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.bookings (id, user_id, tour_id, booking_date, guests, total_price, status, payment_method, notes) FROM stdin;
\.


--
-- Data for Name: favorites; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.favorites (id, user_id, tour_id, created_at) FROM stdin;
1	1	29	2025-05-03 14:37:49.819194
2	1	24	2025-05-03 14:37:53.218557
3	1	26	2025-05-03 15:11:37.754385
4	1	27	2025-05-03 15:14:29.988122
5	2	25	2025-05-03 20:21:52.442252
6	2	30	2025-05-03 20:21:59.126586
8	1	37	2025-05-03 21:09:42.398373
9	1	33	2025-05-03 21:09:43.412114
11	4	26	2025-05-04 10:10:22.506527
12	4	29	2025-05-04 10:10:26.190611
13	3	41	2025-05-04 13:25:19.608077
14	3	39	2025-05-04 13:25:21.318388
15	3	27	2025-05-04 13:25:27.252875
16	3	25	2025-05-04 13:25:28.64807
19	5	42	2025-05-04 13:26:52.169215
20	5	25	2025-05-04 13:26:55.945587
21	5	24	2025-05-04 13:26:58.320877
22	5	27	2025-05-04 13:26:59.286122
23	6	25	2025-05-04 18:25:47.416121
26	6	40	2025-05-04 18:26:10.339134
28	7	42	2025-05-05 14:23:46.496395
30	7	35	2025-05-05 14:23:52.93574
31	7	32	2025-05-05 14:36:58.496401
32	7	41	2025-05-05 14:41:03.090466
33	7	39	2025-05-05 14:41:23.179572
35	8	41	2025-05-06 17:35:42.89025
36	8	40	2025-05-06 17:35:45.72651
37	8	37	2025-05-06 17:35:52.388003
38	8	22	2025-05-06 17:36:05.075612
40	2	38	2025-05-08 11:44:27.239275
42	2	39	2025-05-08 11:44:31.469201
43	2	42	2025-05-08 13:30:57.886646
44	2	41	2025-05-08 13:30:59.93589
\.


--
-- Data for Name: tours; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tours (id, name, short_description, description, price, location, type, date, image, featured, active, created_at) FROM stdin;
21	Samsun Turu	Samsun Kalesi Turu	Tarihi Samsun Kalesi Turu	2400.00	Samsun	historical	2025-04-19	67fed3acc943e_8.png	f	t	2025-04-16 00:46:20.829444
22	İstanbul Turu	İstanbul Turu	İstanbul Turu	1230.00	İstanbul	adaptation	2025-04-17	68001887c59be_image.png	f	t	2025-04-16 23:52:23.816455
24	Muğla Turu	Muğla Deniz Turu	Muğla Deniz ve Festival Turu	2650.00	Muğla	festival	2025-04-22	68001b4de57a1_4.png	t	t	2025-04-17 00:04:13.945836
25	Adana Turu	Adana Adaptasyon Turu	Adana Adaptasyon ve Yöre Turu	3480.00	Adana	adaptation	2025-04-30	6800220b5d785_1.png	t	t	2025-04-17 00:32:59.387411
26	Kars Turu	Kars Kalesi Turu	Kars Kalesi Kaşar Peyniri Festivali	3250.00	Kars	festival	2025-04-28	6800252261564_6.png	t	t	2025-04-17 00:46:10.404646
27	İzmir Turu	İzmir Bombası Festivali	Efsanevi İzmir Bombası Festivali	1790.00	İzmir	festival	2025-04-22	68002d2d03237_5.png	t	t	2025-04-17 01:20:29.017331
29	Bursa Turu	Bursa Cami Turu	Bursa Ulu Cami Turu	1001.00	Bursa	cultural	2025-04-30	68010047c556f_7.png	t	t	2025-04-17 16:21:11.816677
30	İstanbul Turu	İstanbul Turu	İstanbul Turuİstanbul Turuİstanbul Turuİstanbul Turu	1750.00	İstanbul	adaptation	2025-04-23	6801134e3cb81_image.png	t	t	2025-04-17 17:42:22.25652
31	Gaziantep Turu	Gaziantep Yeme İçme ve Gezme Turu	Harika Ötesi Gaziantep Yeme İçme ve Gezme Turu	3489.00	Ankara	festival	2025-05-23	6816574fdd6f5_antep.png	t	t	2025-05-03 20:50:07.911731
32	Ardahan Turu 	Ardahanda yeşilliklere adaptasyon turu	Ardahanda doğa ile iç içe yeşilliklere dolu adaptasyon turu	2345.00	Sivas	adaptation	2025-05-19	681657a18f8d1_ardahan.png	f	t	2025-05-03 20:51:29.592214
33	Balıkesir Turu	Balıkesir Kültürel Miras Turu	Balıkesir'in Tarihi ve Kültürel Miras Turu	2189.00	Erzincan	cultural	2025-05-31	681657e2e87e7_balıkesir.png	t	t	2025-05-03 20:52:34.95639
34	Hakkari Turu	Doğu'nun en yüksek rakımlı şehri Hakkari	Hakkari'de yüksek rakımlı ve doğa ile iç içe bir tur	4320.00	İzmir	adaptation	2025-05-21	681658327237d_hakkari.png	f	t	2025-05-03 20:53:54.471946
35	Karabük Turu	Safranbolu evlerinin tarihi sıcaklığı	Safranbolu evlerinin tarihi sıcaklığı ve kültürel yolculuğu	1400.00	Ankara	cultural	2025-05-15	6816587b3eb74_karabük.png	f	t	2025-05-03 20:55:07.260902
36	Konya Turu	Mevlana Şehri Konya Turu	Mesnevinin diyarı, tarihi ve kültürel büyük Konya Turu	890.00	Bartın	historical	2025-05-18	681658e555a6b_konya.png	f	t	2025-05-03 20:56:53.35486
37	Mardin Turu	Güneşi Selamlama Festivali	Mardin'de güneş, kültürel yiyecekler ve eğlence dolu konserlerle mükemmel büyük bir festival	8790.00	İstanbul	festival	2025-06-19	68165949dc9c1_mardin.png	t	t	2025-05-03 20:58:33.907519
38	Sakarya Turu	Sakarya Kabak Evi Turu	Sakarya Kabak Evi Turu ve Şehir Gezisi	1250.00	Kars	adaptation	2025-07-12	6816599a2de42_sakarya.png	f	t	2025-05-03 20:59:54.191845
39	Sivas Turu	Sivas Kültürel ve Tarihi Yolculuk Turu	Sivas'ta Günümüze kadar Bırakılan Tarihi miraslara Yolculuk Turu	3599.00	Antalya	historical	2025-07-29	681659f63279d_sivas.png	t	t	2025-05-03 21:01:26.210708
40	Trabzon Turu	جولة خاصة في طرابزون وأوزنجول للعرب	جولة خاصة في طرابزون وأوزنجول وفرصة عقارية للعرب	12340.00	Katar	adaptation	2025-07-15	68165a6f4737e_trabzon.png	t	t	2025-05-03 21:03:27.296302
41	Şanlıurfa Turu	Şanlıurfa Balıklıgöl Turu ve Yeme İçme Festvali	Şanlıurfa Balıklıgöl'de Hem Tarihi Hem De Yemeye Doyacağınız Bir Festival Tur 	6980.00	Çanakkale	festival	2025-08-29	68165b2498a6f_urfa.png	t	t	2025-05-03 21:06:28.62996
42	Zonguldak Turu	Zonguldak Ereğli Turu 	Zonguldak Ereğli'de Hem Denize Hem De Mağaralara Doyacağınız Fantastik Bir Turu	2198.00	Gaziantep	historical	2025-09-19	68165b9486726_zonguldak.png	f	t	2025-05-03 21:08:20.555698
43	Samsun Spor Futbol Festivali Turu	Samsun Spor Tesislerinde Harika Bir Futbol Festivali Turu	Samsun Spor Tesislerinde Harika Bir Futbol Festivali Turu	5500.00	Samsun	festival	2025-06-30	681c7bd38b4a0_Adsız tasarım.png	t	t	2025-05-08 12:39:31.578505
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, password, phone, birth_date, reset_token, reset_expires, created_at, last_login) FROM stdin;
4	Selin Karaman	selinsu@hotmail.com	$2y$12$r4zhw3N9MEqwh/eGDCEuceM5.bOzx.0x93iYl8etp278vutXYn4tm	5337546732	2004-05-19	\N	\N	2025-05-04 10:10:04.360303	2025-05-04 13:24:35.447548
5	Oğuzhan Özmen	ozmenoguzhan@hotmail.com	$2y$12$SDLQZiNR.SYmIrtC0MXrI.xehiHn9qaQiYb/Il58EQ.k/psFv3DBG	5437626473	2003-12-01	\N	\N	2025-05-04 13:26:24.692376	2025-05-04 13:26:33.599323
7	sukrank	sukrankurt7@hotmail.com	$2y$12$qiSA3XLyEkzHPlE9OJJqmO4FSZRgK6T9TAOaBEmE/8CuNQIpL6yt2	5555555555	2000-05-05	\N	\N	2025-05-05 14:22:06.876813	2025-05-05 14:22:30.218495
8	hüseyin demirel	hsyndemirel421@gmail.com	$2y$12$4T43IF/mKW5myGSv//uz5umRCyThsXH1zjzRNcqeuGpf.qGimAr36	5305907698	2002-05-06	\N	\N	2025-05-06 17:34:45.891918	2025-05-06 17:35:18.60501
3	Hüseyin Demirel	huseyindemirel@hotmail.com	$2y$12$ghbF5hAhTD2JwSILTFPvjuGIIf3.DhLJA98nDk5Ff7ZHDmMcqzlW2	5436547891	2003-09-02	\N	\N	2025-05-03 22:45:23.113319	2025-05-08 11:46:19.019019
2	Sükran Kurt	sukrankurt@hotmail.com	$2y$12$GQ1HcNtite5KXdcJBJObCeUK1DBIb0kELVtSgcagP8KU924tZw2.6	5338997212	2000-02-01	\N	\N	2025-05-03 20:20:58.18244	2025-05-08 13:29:21.007734
1	Ali Toksoy	gaffartoksoy@hotmail.com	$2y$12$w08DYy4dcg0t3HNlAKg7ZeSOy2PXuD3P4U5cGH5GuoE00HC5Ia.mm	5337004793	2000-01-01	\N	\N	2025-05-01 23:43:11.591537	2025-12-12 10:43:15.872149
6	hasan kurt	hasan@gmail.com	$2y$12$hIg/du3zSay4ZBxOVJnRf.7J2BfwKXakk2Gn3fVhktFM.yhCe0jOK	2761846128	2000-12-13	\N	\N	2025-05-04 18:24:57.362218	2025-12-12 10:44:25.648716
\.


--
-- Name: bookings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.bookings_id_seq', 2, true);


--
-- Name: favorites_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.favorites_id_seq', 44, true);


--
-- Name: tours_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tours_id_seq', 43, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 8, true);


--
-- Name: bookings bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_pkey PRIMARY KEY (id);


--
-- Name: favorites favorites_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favorites
    ADD CONSTRAINT favorites_pkey PRIMARY KEY (id);


--
-- Name: favorites favorites_user_id_tour_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favorites
    ADD CONSTRAINT favorites_user_id_tour_id_key UNIQUE (user_id, tour_id);


--
-- Name: tours tours_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tours
    ADD CONSTRAINT tours_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: bookings bookings_tour_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_tour_id_fkey FOREIGN KEY (tour_id) REFERENCES public.tours(id);


--
-- Name: bookings bookings_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: favorites favorites_tour_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favorites
    ADD CONSTRAINT favorites_tour_id_fkey FOREIGN KEY (tour_id) REFERENCES public.tours(id);


--
-- Name: favorites favorites_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.favorites
    ADD CONSTRAINT favorites_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: alitoksoy
--

GRANT ALL ON SCHEMA public TO postgres;


--
-- PostgreSQL database dump complete
--

