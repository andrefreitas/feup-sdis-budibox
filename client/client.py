
from utils import *
import socket
import json

class Client:
    def __init__(self, email, password):
        self.email = email
        self.password = password
        self.api = "http://apolo.budibox.com/api/"
        
    def get_email(self):
        return self.email
    
    def login(self):
        # Login 
        url = self.api+'login.php'
        values = {'email': self.email,
                  'password': self.password
                  }
        response = json_request(url, values)
        print response      
        return response
    
    def notify_startup(self):
        # Notify Startup
        url = self.api+'computers/notifyStartup.php'
        values= {'user': self.email,
                  'computer': get_computer_name()
                 }
        response = json_request(url, values)
        print response
        
        # Set Location
        if (response['result'] == 'ok'):
            location = utils.get_location()
            url = self.api+'computers/setLocation.php'
            values = {'user': self.email,
                      'computer': socket.gethostname(),
                      'lat': str(location[0]),
                      'lon': str(location[1])
                      }
            print values
            result = utils.json_request(url, values)
            print result
            return result['result'] == 'ok'