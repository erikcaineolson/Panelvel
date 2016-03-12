import sys
import mysql.connector

if len(sys.argv) < 5:
    print "Too few arguments; call like:\npython create_db.py database_name database_user database_pass database_host"
    exit()

#"/python/create_db.py $wp_db $wp_db_user $wp_db_pass $wp_db_host"
wp_database_name = sys.argv[1]
wp_database_user = sys.argv[2]
wp_database_pass = sys.argv[3]
wp_database_host = sys.argv[4]

cnx = mysql.connector.connect(user=wp_database_user, password=wp_database_pass, host=wp_database_host, database=wp_database_name)
cursor = cnx.cursor()

try:
    cursor.execute("CREATE DATABASE {0}".format(wp_database_name))
    cursor.execute("GRANT ALL PRIVILEGES ON {0}.* TO '{1}'@'%' IDENTIFIED BY '{2}'".format(wp_database_name,
                                                                                        wp_database_user,
                                                                                        wp_database_pass))
except mysql.connector.Error as err:
    print "Failed creating database: {}".format(err)
    exit(1)

cnx.close()
