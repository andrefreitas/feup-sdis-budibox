import sys
from PyQt4.QtGui import QIcon
from PyQt4.QtCore import QDateTime, QObject, QUrl, pyqtSignal
from PyQt4 import QtGui
from PyQt4.QtGui import QApplication
from PyQt4.QtDeclarative import QDeclarativeView
from PyQt4 import QtCore
import PySide

class Communicate(QtCore.QObject):
    
    closeApp = QtCore.pyqtSignal() 
    

class Example(QtGui.QMainWindow):
    def __init__(self):
        self.initUI()
        
    def initUI(self):      
        self.c = Communicate()
        self.c.closeApp.connect(self.close)       
        
        view = QDeclarativeView()
        view.setSource(QUrl('qml/main.qml'))
        view.setWindowTitle("Budibox")
        view.setWindowIcon(QIcon('/qml/budibox.jpg'))
        
        context = view.rootContext()
        
        # Display the user interface and allow the user to interact with it.
        view.setGeometry(360, 360, 360, 360)
        view.setMaximumSize(360, 360)
        view.show()
        
        raw_input()
        
    def mousePressEvent(self, event):
        self.c.closeApp.emit()
        
        
def main():
    
    app = QtGui.QApplication(sys.argv)
    ex = Example()


if __name__ == '__main__':
    main()
