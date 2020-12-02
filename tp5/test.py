import pymysql as p
conn = p.connect ("127.0.0.1","root","888888","tp5")
cursor = conn.cursor()
cursor.execute("use tp5")
sql = """insert into user (
user_name,mobile,email,status) values(
"xiaowei","18611695397",'b@b.com',1)"""
cursor.execute(sql)
conn.commit()
cursor.close()
conn.close()