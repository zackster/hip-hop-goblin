import urllib, MySQLdb

db = MySQLdb.connect("localhost", "root", "root", db='hiphopgoblin', unix_socket='/Applications/MAMP/tmp/mysql/mysql.sock')
cursor = db.cursor()
sql = '''SELECT id,filename FROM songs WHERE filename like \'%amazon%\''''
cursor.execute(sql)
data = cursor.fetchall()
for row in data:
	songid = row[0]
	dirname = '%04d' % (1 + (row[0] / 128))
	old_filename = row[1]
	new_filename = '/songs/' + dirname + '/' + str(songid) + '.mp3'
	print songid,dirname,old_filename, new_filename
	# try:
	# 	urllib.urlretrieve(old_filename, '/srv/www/hiphopgoblin.com/public_html' + new_filename)
	# 	sql = 'UPDATE songs SET filename=%s WHERE filename=%s' % (new_filename, old_filename)
	# 	cursor.execute(sql)
	# except Exception, e:
	# 	print "There was a problem: %s" % e
		