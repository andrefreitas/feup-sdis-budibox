from file import File
import httplib
import urllib
import urllib2
import json

class Client:
    def __init__(self, name, email, password):
        self.name = name
        self.email = email
        self.password = password
        self.api = "http://master.budibox.com/api/"
    
    def login(self):
        data = 'login.php?auth=12&email="'+self.email+'"&password="'+self.password+'"'
        self.request(data)
    
    def request(self, data):
        request = urllib.urlopen(self.api + data)
        json_response = json.load(request) 
        print json_response['result']
        if (json_response['result'] == 'error'):
            print "Username or password incorrect. Please try again or register at http://www.budibox.com!"
        else:
            print "Login successful!"

c = Client('ana', 'tt', 'maasd')
c.login()
        