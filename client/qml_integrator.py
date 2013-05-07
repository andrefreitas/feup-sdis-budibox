import sys

from PySide.QtGui import QApplication
from PySide.QtDeclarative import QDeclarativeView
from PySide.QtCore import QDateTime, QObject, QUrl
from PySide import QtCore


class Test( QtCore.QObject ):
    def __init__( self ):
        QtCore.QObject.__init__(self)
 
    @QtCore.Slot('QString', 'QString')
    def printText(self,email,password):
        print email
        print password
 

app = QApplication(sys.argv)

# Create the QML user interface.
view = QDeclarativeView()
view.setSource(QUrl('qml/main.qml'))
view.setWindowTitle("Budibox")
#view.setWindowIcon(QIcon('/qml/budibox.jpg'))

context = view.rootContext()
context.setContextProperty("testModel",Test())

# Display the user interface and allow the user to interact with it.
view.setGeometry(360, 360, 360, 360)
view.setMaximumSize(360, 360)
view.show()

app.exec_()