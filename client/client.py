
import utils

class Client:
    def __init__(self, email, password):
        self.email = email
        self.password = password
        self.api = "http://master.budibox.com/api/"
    
    def login(self):
        url = self.api+'login.php'
        values = {'email': self.email,
                  'password': self.password
                  }
        response = utils.json_request(url, values)
        print response
        return response

     