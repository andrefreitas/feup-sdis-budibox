import sys

from PySide.QtGui import QApplication, QIcon
from PySide.QtDeclarative import QDeclarativeView
from PySide.QtCore import QDateTime, QObject, QUrl
from PySide import QtCore

from client import Client
from data_manager import DataManager

import base64
import ctypes


def Mbox(title, text, style):
        ctypes.windll.user32.MessageBoxA(0, text, title, style)

class Receive_data( QtCore.QObject ):
    def __init__( self ):
        self.data = DataManager("data.db")
        QtCore.QObject.__init__(self)

    @QtCore.Slot('QString', 'QString')
    def execute(self,email,password):
        c = Client(email, password)
        response = c.login()
        print email
        print password
        if (response['result'] == 'missingParams'):
            Mbox('Budibox', 'Username or password incorrect. Please try again or register at http://www.budibox.com!', 0)
        else:
            if (response['result'] == 'ok'):
                startup = c.notify_startup()
                if (not startup):
                    print 'ERROR'
                else:
                    print 'Notified'
            self.data.addProperty("email", email);
            password = base64.b64encode(password)
            print password
            self.data.addProperty("password", str(password));
            Mbox('Budibox', 'Login successful!', 0)
            exit()        
        


data = DataManager("data.db")
email = data.getPropertyValue("email")
password = data.getPropertyValue("password")


if (len(email) > 0 and len(password) > 0):
    email = str(email[0])
    password = str(base64.b64decode(password[0]))
    c = Client(email, password)
    response = c.login()

    if (response['result'] == 'ok'):
        startup = c.notify_startup()
        if (not startup):
            print 'ERROR'
        else:
            print 'Notified'

else:
    Mbox("Budibox", "Credentials undefined or incorrect. Please login again.", 0)

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
    
    app.exec_()