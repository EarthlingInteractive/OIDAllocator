CREATE TABLE "public"."user" (
	"id" BIGINT NOT NULL DEFAULT nextval('public.newentityid'),
	"username" VARCHAR(126) NOT NULL,
	"passhash" VARCHAR(126),
	"emailaddress" VARCHAR(126),
	PRIMARY KEY ("id")
);
CREATE TABLE "public"."organization" (
	"id" BIGINT NOT NULL DEFAULT nextval('public.newentityid'),
	"name" VARCHAR(126) NOT NULL,
	PRIMARY KEY ("id")
);
CREATE TABLE "public"."userorganizationattachment" (
	"userid" BIGINT NOT NULL,
	"organizationid" BIGINT NOT NULL,
	PRIMARY KEY ("userid", "organizationid"),
	FOREIGN KEY ("userid") REFERENCES "public"."user" ("id"),
	FOREIGN KEY ("organizationid") REFERENCES "public"."organization" ("id")
);
CREATE TABLE "public"."computationstatus" (
	"statuscode" VARCHAR(126) NOT NULL,
	PRIMARY KEY ("statuscode")
);
CREATE TABLE "public"."computation" (
	"expression" VARCHAR(126) NOT NULL,
	"statuscode" VARCHAR(126) NOT NULL,
	"result" VARCHAR(126),
	PRIMARY KEY ("expression"),
	FOREIGN KEY ("statuscode") REFERENCES "public"."computationstatus" ("statuscode")
);
