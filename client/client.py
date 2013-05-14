
import utils
import socket
import json

class Client:
    def __init__(self, email, password):
        self.email = email
        self.password = password
        self.api = "http://master.budibox.com/api/"
    
    def login(self):
        # Login 
        url = self.api+'login.php'
        values = {'email': self.email,
                  'password': self.password
                  }
        
        response = utils.json_request(url, values)
        print response        
        return response
    
    def notify_startup(self):
        # Notify Startup
        url = self.api+'/computers/notifyStartup.php'
        values= {'user': self.email,
                  'computer': socket.gethostname()
                 }
        
        response = utils.json_request(url, values)
        print response
        
        # Set Location
        if (response['result'] == 'ok'):
            location = utils.get_location()
            url = self.api+'/computers/setLocation.php'
            values = {'user': self.email,
                      'computer': socket.gethostname(),
                      'lat': str(json.loads(location)['latitude']),
                      'lon': str(json.loads(location)['longitude'])
                      }
            print values
            result = utils.json_request(url, values)
            print result
            if (result['result'] == 'ok'):
                return True
            else:
                return False
        
        return False

        
