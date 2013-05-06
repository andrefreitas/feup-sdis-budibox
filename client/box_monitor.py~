import time
from watchdog.observers import Observer
from watchdog.events import FileSystemEventHandler
from file import File
from client import Client

class BoxMonitor(FileSystemEventHandler):
    def __init__(self, box_directory):
        self.box_directory = box_directory
    
    def created_handler(self, event):
        print "CREATED " + str(event)
    
    def deleted_handler(self, event):
        print "DELETED " + str(event)
        
    def modified_handler(self, event):
        print "MODIFIED " + str(event)
    
    def moved_handler(self, event):
        print "MOVED " + str(event)
     
    def on_moved(self, event):
        self.moved_handler(event)

    def on_created(self, event):
        self.created_handler(event)

    def on_deleted(self, event):
        self.deleted_handler(event)

    def on_modified(self, event):
        self.modified_handler(event)
    
    def start(self):
       event_handler = self
       observer = Observer()
       observer.schedule(event_handler, self.box_directory, recursive=False)
       observer.start()
       try:
           while True:
               time.sleep(1)
       except KeyboardInterrupt:
           observer.stop()
       observer.join()
       
       
c = BoxMonitor('C:\\Users\\Ana Gomes\\Documents')
c.start()