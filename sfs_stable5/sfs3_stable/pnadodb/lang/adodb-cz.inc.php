<?php
//$Id: adodb-cz.inc.php 5310 2009-01-10 07:57:56Z hami $
# Czech language, encoding by ISO 8859-2 charset (Iso Latin-2)
# For convert to MS Windows use shell command:
#    iconv -f ISO_8859-2 -t CP1250 < adodb-cz.inc.php
# For convert to ASCII use shell command:
#    unaccent ISO_8859-2 < adodb-cz.inc.php
# v1.0, 19.06.2003 Kamil Jakubovic <jake@host.sk>

$ADODB_LANG_ARRAY = array (
            'LANG'                      => 'cz',
            DB_ERROR                    => 'neznám?chyba',
            DB_ERROR_ALREADY_EXISTS     => 'ji? existuje',
            DB_ERROR_CANNOT_CREATE      => 'nelze vytvo?it',
            DB_ERROR_CANNOT_DELETE      => 'nelze smazat',
            DB_ERROR_CANNOT_DROP        => 'nelze odstranit',
            DB_ERROR_CONSTRAINT         => 'poru?en?omezujíc?podmínky',
            DB_ERROR_DIVZERO            => 'd?len?nulou',
            DB_ERROR_INVALID            => 'neplatn?,
            DB_ERROR_INVALID_DATE       => 'neplatn?datum nebo ?as',
            DB_ERROR_INVALID_NUMBER     => 'neplatn??íslo',
            DB_ERROR_MISMATCH           => 'nesouhlas?,
            DB_ERROR_NODBSELECTED       => '?ádn?databáze nen?vybrána',
            DB_ERROR_NOSUCHFIELD        => 'pole nenalezeno',
            DB_ERROR_NOSUCHTABLE        => 'tabulka nenalezena',
            DB_ERROR_NOT_CAPABLE        => 'nepodporováno',
            DB_ERROR_NOT_FOUND          => 'nenalezeno',
            DB_ERROR_NOT_LOCKED         => 'nezam?eno',
            DB_ERROR_SYNTAX             => 'syntaktick?chyba',
            DB_ERROR_UNSUPPORTED        => 'nepodporováno',
            DB_ERROR_VALUE_COUNT_ON_ROW => '',
            DB_ERROR_INVALID_DSN        => 'neplatn?DSN',
            DB_ERROR_CONNECT_FAILED     => 'p?ipojen?selhalo',
            0	                        => 'bez chyb', // DB_OK
            DB_ERROR_NEED_MORE_DATA     => 'málo zdrojových dat',
            DB_ERROR_EXTENSION_NOT_FOUND=> 'roz??en?nenalezeno',
            DB_ERROR_NOSUCHDB           => 'databáze neexistuje',
            DB_ERROR_ACCESS_VIOLATION   => 'nedostate?n?práva'
);
?>
