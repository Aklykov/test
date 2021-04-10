create table if not exists aklykov_currencyrate
(
	ID int(11) not null auto_increment,
	CODE varchar(10) null,
	DATE_CREATE datetime,
	COURSE float(10),
	PRIMARY KEY(ID)
);