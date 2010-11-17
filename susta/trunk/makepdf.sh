#!/bin/bash

# Author: Daniele Primon, Nicola Busanello
echo -----------INIZIO
date	#per debug

export PATH=$PATH:/usr/bin/X11 
export LANG=it_IT@euro
export HOME=/var/www

pushd ~
#/usr/bin/openoffice "macro:///Standard.converter.WriterToPDF($1)"
/usr/bin/openoffice -display :99 -invisible "macro:///Standard.converter.WriterToPDF($1)"
popd

date	#per debug
