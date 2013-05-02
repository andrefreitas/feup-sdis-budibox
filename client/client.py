from file import File
import utils

class Client:
    def __init__(self, name, email, password):
        self.name = name
        self.email = email
        self.password = password
        self.api = "http://master.budibox.com/api/"
    
    def login(self):
        url = self.api+'login.php'
        values = {'auth': '12',
                  'email': self.email,
                  'password': self.password
                  }
        response = utils.json_request(url, values)
        print response
        if (response['result'] == 'error'):
            print "Username or password incorrect. Please try again or register at http://www.budibox.com!"
        else:
            print "Login successful!"

c = Client('ana', 'a', 'a')
c.login()
     