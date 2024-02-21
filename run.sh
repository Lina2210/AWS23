#!/bin/bash
# directori del script run.sh
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
# entrem a la carpeta del codi font
cd $SCRIPT_DIR/src
# engeguem el PHP server
php -S localhost:8080

