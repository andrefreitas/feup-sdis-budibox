# Budibox

Budibox is a P2P cloud storage service for Linux and Windows.
This project has been developed in the distributed systems sources
(SDIS) at Faculty of Engineering of University of Porto. The current
release is not stable and may have bugs.

## Installation

It is necessary to install the desktop client to synchronize
the files. Please download at (BUDIBOX WEBSITE)

## Usage

Create an account at (BUDIBOX WEBSITE),
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

# License

Copyright (c) 2015 Faculty of Engineering of the University of Porto

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
