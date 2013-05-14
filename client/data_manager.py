import sqlite3

class DataManager:
    def __init__(self, database):
       self.conection = sqlite3.connect(database)
       self.cursor = self.conection.cursor()
    
    def query(self,query):
        self.cursor.execute(query)
        self.conection.commit()
        return self.cursor.fetchall()
    
    def getPropertyValue(self, property):
         result = self.query("SELECT value FROM properties WHERE property =\""+property+"\"")
         return result[0] if len(result)>0 else []
     
    def addProperty(self, property_name, value):
        sql = "SELECT * FROM properties WHERE property =\""+property_name+"\""
        result = self.query("SELECT * FROM properties WHERE property =\""+property_name+"\"")
        if (len(result) > 0):
            self.cursor.execute("UPDATE properties SET value=? WHERE property = ?" ,( str(value), str(property_name)))
            self.conection.commit()
            return True
        else:
            self.query("INSERT INTO properties(property,value) VALUES( '" + property_name + "' , '" + value + "' )")
            return True
            
         
        