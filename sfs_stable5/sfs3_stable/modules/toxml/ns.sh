#! /bin/sh

# $Id: ns.sh 5310 2009-01-10 07:57:56Z hami $

SP_CHARSET_FIXED=YES
export SP_CHARSET_FIXED

SP_ENCODING=XML
export SP_ENCODING

SGML_CATALOG_FILES="./pubtext/xml.soc"
export SGML_CATALOG_FILES

nsgmls -s -wxml $1
