from dotenv import load_dotenv
import os
import sys
import mysql.connector

load_dotenv()

if len(sys.argv) < 5:
    print "Too few arguments; call like:\npython create_db.py database_name database_user database_pass database_host"
    exit()

#"/python/create_db.py $wp_db $wp_db_user $wp_db_pass $wp_db_host"
wp_database_name = sys.argv[1]
wp_database_user = sys.argv[2]
wp_database_pass = sys.argv[3]
wp_database_host = sys.argv[4]

cnx = mysql.connector.connect(
    host=os.getenv('DB_HOST'),
    user=os.getenv('DB_USER'),
    passwd=os.getenv('DB_PASS')
)
cursor = cnx.cursor()

try:
    query = "CREATE DATABASE %s; GRANT ALL PRIVILEGES ON %s.* TO '%s'@'%s' IDENTIFIED BY '%s';"
    parameters = (wp_database_name, wp_database_name, wp_database_username, os.getenv('DB_HOST'),
                  wp_database_password)

    db_cursor.execute(query, parameters)
    db.commit()

except mysql.connector.Error as err:
    print "Failed creating database: {}".format(err)
    exit(1)

cnx.close()
