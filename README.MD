# Budibox

Budibox is a P2P cloud storage service for Linux and Windows.
This project has been developed in the distributed systems sources
(SDIS) at Faculty of Engineering of University of Porto. The current
release is not stable and may have bugs.

## Installation

It is necessary to install the desktop client to synchronize
the files. Please download at https://andrefreitas.pt/budibox

## Usage

Create an account at https://andrefreitas.pt/budibox,
install the desktop client and place your files in the
Budibox folder to start the synchronization. On the website,
you can login and see your list of files, peers and change
your space quota. The more space you offer, the more you have to host
your files. All the files are encrypted in the peers.

## Development dependencies
The server must have the following:
- PHP5.4 with MongoDB client
- MongoDB

The development computer must have:
- Python 2.7 with PySide
- Sqlite3

In the client, run the following commands:
```
$ sqlite3 data.db < db_setup.sql
$ python client_daemon.py
```

## Credits

- Ana Gomes - acgg.gomes@fe.up.pt
- André Freitas - freitas.andre@fe.up.pt
