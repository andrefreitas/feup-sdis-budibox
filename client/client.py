
from utils import *
import socket
import json

class Client:
    def __init__(self, email, password):
        self.email = email
        self.password = password
        self.api = "https://andrefreitas.pt/budibox/api/"
        location = get_location()
        self.lat = location[0]
        self.lon = location[1]

    def get_email(self):
        return self.email

    def get_lat(self):
        return self.lat

    def get_lon(self):
        return self.lon

    def login(self):
        # Login
        url = self.api+'login.php'
        values = {'email': self.email,
                  'password': self.password
                  }
        response = json_request(url, values)

        return response

    def notify_startup(self):
        # Notify Startup
        url = self.api+'computers/notifyStartup.php'
        values= {'apikey': '12',
                 'user': self.email,
                  'computer': get_computer_name()
                 }
        response = json_request(url, values)

        if (response['result'] != 'ok'):
            print_message("Error trying to notify startup: user " + self.email + " computer " + get_computer_name())


        #Set Location
        if (response['result'] == 'ok'):
            url = self.api+'computers/setLocation.php'
            values = {'apikey': '12',
                      'user': str(self.email),
                      'computer': get_computer_name(),
                      'lat': str(self.lat),
                      'lon': str(self.lon)
                      }

            result = json_request(url, values)

            return result['result'] == 'ok'
