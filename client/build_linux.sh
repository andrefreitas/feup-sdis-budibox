cxfreeze client_daemon.py --target-dir dist --include-modules atexit,PySide.QtNetwork --icon qml/budibox.ico
cp -rf qml dist
cp db_setup.sql dist
cd dist
sqlite3 data.db < db_setup.sql
rm -rf db_setup.sql
