CREATE SCHEMA IF NOT EXISTS public;

CREATE TABLE "public"."schemaupgrade" (
	"time" TIMESTAMP,
	"scriptfilename" VARCHAR(255),
	"scriptfilehash" CHAR(40)
);
