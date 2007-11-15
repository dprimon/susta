#!/bin/bash

echo -----------INIZIO
date	#per debug

export PATH=$PATH:/usr/bin/X11 
export LANG=it_IT@euro
export HOME=/var/www

pushd ~
#/usr/local/bin/openoffice -display :0 "macro:///Standard.converter.WriterToPDF('$1')"
/usr/local/bin/openoffice -display :99 -invisible "macro:///Standard.converter.WriterToPDF('$1')"
popd

date	#per debug
