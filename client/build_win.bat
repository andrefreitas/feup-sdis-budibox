cxfreeze client_daemon.py --target-dir dist --include-modules atexit,PySide.QtNetwork --icon qml/budibox.ico
copy qml dist
copy db_setup.sql dist
cd dist
sqlite3 data.db < db_setup.sql
del db_setup.sql