import sys

from PySide.QtGui import QApplication, QIcon
from PySide.QtDeclarative import QDeclarativeView
from PySide.QtCore import QDateTime, QObject, QUrl
from PySide import QtCore

from client import Client

class Receive_data( QtCore.QObject ):
    def __init__( self ):
        QtCore.QObject.__init__(self)
 
    @QtCore.Slot('QString', 'QString')
    def execute(self,email,password):
        c = Client(email, password)
        response = c.login()
        print email
        print password
        if (response['result'] == 'missingParams'):
            print "Username or password incorrect. Please try again or register at http://www.budibox.com!"
        else:
            print "Login successful!"
            exit()        
        
        
 

app = QApplication(sys.argv)

# Create the QML user interface.
view = QDeclarativeView()
view.setSource(QUrl('qml/main.qml'))
view.setWindowTitle("Budibox")
view.setWindowIcon(QIcon("qml/budibox.jpg"))

context = view.rootContext()
context.setContextProperty("send_data",Receive_data())

# Display the user interface and allow the user to interact with it.
view.setGeometry(360, 360, 360, 360)
view.setMaximumSize(360, 360)
view.show()

