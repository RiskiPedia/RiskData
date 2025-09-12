-- see https://www.mediawiki.org/wiki/Manual:Coding_conventions/Database

--
-- Data saved from <riskdata> tags.
--
CREATE TABLE /*_*/riskdata_meta (
  dtm_table varchar(255) NOT NULL PRIMARY KEY,
  -- pipe-separated list of column names
  dtm_columns text NOT NULL
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/dtm_table ON /*_*/riskdata_meta(dtm_table);
